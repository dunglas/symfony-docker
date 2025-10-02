# Non-root user

Following [Docker best practices](https://docs.docker.com/build/building/best-practices/#user), it is recommended to run your services as non-root user whenever possible.

## Apply changes

You can apply the following patches to your `Dockerfile`, `compose.override.yaml` and `compose.prod.yaml` to run the FrankenPHP container as non-root for development and production usage.

`Dockerfile`

```diff
+ARG PUID=${PUID:-1000}
+ARG PGID=${PGID:-1000}
+ARG USER=${USER:-frankenphp}
+ARG GROUP=${GROUP:-frankenphp}

 # Versions
 FROM dunglas/frankenphp:1-php8.4 AS frankenphp_upstream

 # Base FrankenPHP image
 FROM frankenphp_upstream AS frankenphp_base

+ARG PUID
+ARG PGID
+ARG USER
+ARG GROUP
+
 WORKDIR /app

-VOLUME /app/var/

 # ...

+RUN set -eux; \
+    groupadd -g $PGID $GROUP; \
+    useradd -u $PUID -g $PGID --no-create-home $USER; \
+    mkdir -p var/cache var/log; \
+    chown -R $PUID:$PGID /data/ /config/ var/
+
 ENTRYPOINT ["docker-entrypoint"]

 # ...

 # Dev FrankenPHP image
 FROM frankenphp_base AS frankenphp_dev

+ARG USER
+
 ENV APP_ENV=dev
 ENV XDEBUG_MODE=off
 ENV FRANKENPHP_WORKER_CONFIG=watch

 COPY --link frankenphp/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/

+USER $USER
+
 CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--watch" ]

 # Prod FrankenPHP image
 FROM frankenphp_base AS frankenphp_prod

+ARG PUID
+ARG PGID
+ARG USER
+
 ENV APP_ENV=prod

 # ...

 RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer dump-env prod; \
    composer run-script --no-dev post-install-cmd; \
-   chmod +x bin/console; sync;
+   chmod +x bin/console; \
+   chown -R $PUID:$PGID var/; sync
+
+USER $USER
```

`compose.override.yaml`

```yaml
services:
  php:
    build:
      context: .
      target: frankenphp_dev
+     args:
+       PUID: ${PUID:-1000}
+       PGID: ${PGID:-1000}
+   user: "${PUID:-1000}:${PGID:-1000}"
```

`compose.prod.yaml`

```yaml
services:
  php:
    build:
      context: .
      target: frankenphp_prod
+     args:
+       PUID: ${PUID:-1000}
+       PGID: ${PGID:-1000}
+   user: "${PUID:-1000}:${PGID:-1000}"
```

## Usage

After applying the previous changes, you have to pass the `PUID` and `PGID` as environment variables to the Dockerfile.

You can do this in a myriad of different ways:

- Export your `PUID` and `PGID` to your current shell before running `docker compose`.

  ```console
  export PUID=$(id -u); export PGID=$(id -g); docker compose ...
  ```

- Pass `PUID` and `PGID` directly to `docker compose`.

  ```console
  PUID=$(id -u) PGID=$(id -g) docker compose ...
  ```

- Add `PUID` and `PGID` to your dotenv (`.env`) file.

  ```dotenv
  PUID=1000
  PGID=1000
  ```

  > [!CAUTION]
  > This method is not recommended as it can cause issues in CI environment where the runner has a different UID/GID.

- Use third-party tools, like [`Task`](https://taskfile.dev/), to do the heavy lifting for you.

  ```yaml
  version: "3"

  env:
    PUID:
      sh: id -u
    PGID:
      sh: id -g

  tasks:
    up:
      desc: Up stack
      cmds:
        - docker compose ...
  ```
