# Monitoring with Ember

[Ember](https://github.com/alexandre-daubois/ember) is a real-time terminal dashboard for Caddy and FrankenPHP.
It is not enabled by default: this guide explains how to run it as a sidecar alongside the `php` service.

## Development

Add the following to `compose.override.yaml`,
merging the `CADDY_GLOBAL_OPTIONS` variable into the existing `environment` of the `php` service:

```yaml
services:
  php:
    environment:
      CADDY_GLOBAL_OPTIONS: |
        metrics {
          per_host
        }

  ember:
    image: alexandredaubois/ember:1
    network_mode: service:php
    depends_on:
      - php
    command: ["--daemon", "--expose", "127.0.0.1:9191"]
    # nobody: the image has no USER (root by default), numeric because scratch has no /etc/passwd
    user: "65534:65534"
    read_only: true
    cap_drop:
      - ALL
    security_opt:
      - no-new-privileges:true
```

Then restart the stack:

```bash
docker compose up --wait
```

The sidecar connects to the Caddy admin API on `localhost:2019` and detects FrankenPHP automatically.
To open the interactive dashboard:

```bash
docker compose exec ember /ember
```

For a one-line health summary, use:

```bash
docker compose exec ember /ember status
```

The Ember image is built from `scratch`, so it has no shell or debugging tools.
Check its output with `docker compose logs ember`.

`CADDY_GLOBAL_OPTIONS` enables per-host Caddy metrics, which Ember needs to display traffic.
Thanks to `per_host`, Ember shows one row per site address listed in `SERVER_NAME` (`localhost` and `php` by default)
instead of Caddy's internal server names (`srv0`, `srv1`).
Rows are values of the `Host` header, not containers:
the `localhost` row is your application as reached from the browser,
and the `php` row is the internal address used by other containers (e.g. to publish Mercure updates).
Ember's own calls go to the admin API and are not counted as HTTP traffic.

Once `CADDY_GLOBAL_OPTIONS` is set in `compose.override.yaml`,
a value defined in your `.env` file is ignored in development.
If you need other global options, add them to `CADDY_GLOBAL_OPTIONS` in `compose.override.yaml`,
next to the `metrics` block.

Ember reads CPU and memory usage from Caddy's `process_*` metrics;
it does not need access to the `php` container's process namespace.

For security, Ember shares the `php` service's network namespace so the Caddy admin API stays on loopback.
Its Prometheus endpoint listens only on `127.0.0.1:9191`; no port is published to the host or other containers.
The container also runs as a non-root user with a read-only filesystem, all Linux capabilities dropped,
and `no-new-privileges` enabled.

## Production

To run Ember in production, add a similar service to `compose.prod.yaml` and enable Caddy metrics on the `php` service:

```yaml
services:
  php:
    environment:
      CADDY_GLOBAL_OPTIONS: |
        metrics {
          per_host
        }

  ember:
    image: alexandredaubois/ember:1
    network_mode: service:php
    depends_on:
      - php
    command: ["--daemon", "--expose", "127.0.0.1:9191"]
    environment:
      EMBER_METRICS_AUTH: "${EMBER_METRICS_AUTH:?}"
    # nobody: the image has no USER (root by default), numeric because scratch has no /etc/passwd
    user: "65534:65534"
    read_only: true
    cap_drop:
      - ALL
    security_opt:
      - no-new-privileges:true
```

Set `EMBER_METRICS_AUTH` to a strong username and password using your deployment's secret management;
the stack refuses to start if it is not defined.
If you customize `CADDY_GLOBAL_OPTIONS`, make sure its value still includes the `metrics` block with `per_host`.

The example deliberately does not publish either port.
Keep the Caddy admin API (`2019`) private;
if you need Prometheus to scrape Ember, choose an appropriate binding and network policy for your deployment,
protect the endpoint with `EMBER_METRICS_AUTH`, and do not expose it publicly without additional access controls.

## Replicated `php` service

The sidecar above monitors a single `php` container.
Docker Compose has no notion of pods: when the `php` service is scaled,
every replica of a `network_mode: service:php` sidecar joins the network namespace of the same container,
so the other replicas are not monitored.

Instead, a single Ember can poll every replica and add an `ember_instance` label to each exported metric
(see [Ember's multi-instance guide](https://github.com/alexandre-daubois/ember/blob/main/docs/multi-instance.md)).
This requires the Caddy admin API to listen on the Compose network.

The ports published by the `php` service in `compose.yaml` prevent running several replicas:
before using this example, remove them and put a load balancer in front of the `php` service.

```yaml
services:
  php:
    deploy:
      replicas: 2
    environment:
      CADDY_GLOBAL_OPTIONS: |
        admin :2019
        metrics {
          per_host
        }

  ember:
    image: alexandredaubois/ember:1
    depends_on:
      - php
    command: ["--daemon", "--expose", ":9191"]
    environment:
      EMBER_ADDR: php_1=http://${COMPOSE_PROJECT_NAME}-php-1:2019;php_2=http://${COMPOSE_PROJECT_NAME}-php-2:2019
      EMBER_METRICS_AUTH: "${EMBER_METRICS_AUTH:?}"
    # nobody: the image has no USER (root by default), numeric because scratch has no /etc/passwd
    user: "65534:65534"
    read_only: true
    cap_drop:
      - ALL
    security_opt:
      - no-new-privileges:true
```

Prometheus can scrape `http://ember:9191/metrics` from the Compose network with the `EMBER_METRICS_AUTH` credentials;
do not publish this port.
`docker compose exec ember /ember status` prints one line per replica.
The interactive dashboard shows one instance at a time:
`docker compose exec ember /ember --addr http://<project>-php-2:2019`.

> [!WARNING]
>
> The Caddy admin API gives full control over the server configuration.
> With `admin :2019`, any container attached to the same network can reach it:
> never publish this port, and only attach trusted services to the network of the `php` service.

Replicas are listed by container name (`<project>-php-<index>`):
update `EMBER_ADDR` when you change the number of replicas.

## Troubleshooting

If Ember shows hosts named `srv0`, `srv1`, etc., Caddy metrics are not labeled per host:
check that `CADDY_GLOBAL_OPTIONS` contains the `metrics` block with `per_host`
(`docker compose exec php printenv CADDY_GLOBAL_OPTIONS`), then restart the stack.

If Ember does not show traffic for a host,
check that the hostname matches one of the site addresses listed in `SERVER_NAME`.
