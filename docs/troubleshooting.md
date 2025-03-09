# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## TLS/HTTPS Issues

See more in the [TLS section](tls.md)

## Production issues

### How to properly build fresh images for production use

Remember that, by default, if you run `docker compose up -d`, only the files `compose.yaml` and `compose.override.yaml` will be used.
See https://docs.docker.com/compose/intro/compose-application-model.

If you need to build images for production environment, you have to use the following command:
`docker compose -f compose.yaml -f compose.prod.yaml build --no-cache`.

### Why application outputs `phpinfo()`

Both dev and prod images have the same image tag (`<...>app-php:latest`). This can cause confusion when working with images.
It is important to make sure that your image is the appropriate one for the current environment.

If you are not careful about this, and try to run your production container(s) with
`docker compose -f compose.yaml -f compose.prod.yaml up -d`
without the right build process beforehand, your application **will still launch**, but will be displaying an output of `phpinfo()` (or possibly even a HTTP 500 error page).

See details below.

<details>

<summary>Output of a basic build process</summary>

In the case of a dev image, you need the `compose.yaml` and `compose.override.yaml` files. Which are the default files for Docker Compose.

This means that running `docker compose <command>` or `docker compose -f compose.yaml -f compose.override.yaml <command>` is the same thing.

```
> docker compose build --no-cache
[+] Building 287.5s (21/21) FINISHED                                                                                                                                                        docker:desktop-linux
=> [php internal] load build definition from Dockerfile                                                                                                                                                    0.0s
=> => transferring dockerfile: 2.96kB                                                                                                                                                                      0.0s
=> [php] resolve image config for docker-image://docker.io/docker/dockerfile:1                                                                                                                             1.7s
=> [php auth] docker/dockerfile:pull token for registry-1.docker.io                                                                                                                                        0.0s
=> CACHED [php] docker-image://docker.io/docker/dockerfile:1@sha256:4c68376a702446fc3c79af22de146a148bc3367e73c25a5803d453b6b3f722fb                                                                       0.0s
=> [php internal] load metadata for docker.io/dunglas/frankenphp:1-php8.3                                                                                                                                  0.7s
=> [php auth] dunglas/frankenphp:pull token for registry-1.docker.io                                                                                                                                       0.0s
=> [php internal] load .dockerignore                                                                                                                                                                       0.0s
=> => transferring context: 498B                                                                                                                                                                           0.0s
=> [php frankenphp_upstream 1/1] FROM docker.io/dunglas/frankenphp:1-php8.3@sha256:80a0db5e0b3bec1c93067407fe0a9047e6af41202d5e476c0a3c2101238ce4f6                                                        0.0s
=> [php internal] load build context                                                                                                                                                                       0.1s
=> => transferring context: 260B                                                                                                                                                                           0.1s
=> CACHED [php frankenphp_base 1/7] WORKDIR /app                                                                                                                                                           0.0s
=> [php frankenphp_base 2/7] RUN apt-get update && apt-get install -y --no-install-recommends     acl     file     gettext     git     curl     && rm -rf /var/lib/apt/lists/*                            35.3s
=> [php frankenphp_base 3/7] RUN set -eux;     install-php-extensions         @composer         apcu         intl         opcache         zip         gd         zlib     ;                              191.6s
=> [php frankenphp_base 4/7] RUN install-php-extensions pdo_pgsql                                                                                                                                         28.7s
=> [php frankenphp_base 5/7] COPY --link frankenphp/conf.d/10-app.ini /usr/local/etc/php/app.conf.d/                                                                                                       0.0s
=> [php frankenphp_base 6/7] COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint                                                                                      0.0s
=> [php frankenphp_base 7/7] COPY --link frankenphp/Caddyfile /etc/caddy/Caddyfile                                                                                                                         0.0s
=> [php frankenphp_dev 1/3] RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini"                                                                                                   0.2s
=> [php frankenphp_dev 2/3] RUN set -eux;     install-php-extensions         xdebug     ;                                                                                                                 28.6s
=> [php frankenphp_dev 3/3] COPY --link frankenphp/conf.d/20-app.dev.ini /usr/local/etc/php/app.conf.d/                                                                                                    0.0s
=> [php] exporting to image                                                                                                                                                                                0.2s
=> => exporting layers                                                                                                                                                                                     0.2s
=> => writing image sha256:5a10dca86d148f93f2452f779f780f0f34628e7aa86b3a0eca40aa327075840e                                                                                                                0.0s
=> => naming to docker.io/library/app-php                                                                                                                                                                  0.0s
=> [php] resolving provenance for metadata file                                                                                                                                                            0.0s
[+] Building 1/1
✔ php  Built
```

And in doing so, images `frankenphp_base` and `frankenphp_dev` are built. And not `frankenphp_prod`.
Good enough for dev purposes.

Then, you can start your dev container(s) by running: `docker compose up -d`.

</details>

<br>

<details>

<summary>Output expected for the production build process</summary>

Start by building the production image.
You <ins>have to</ins> specify the `compose.yaml` and `compose.prod.yaml` files.

```
> docker compose -f compose.yaml -f compose.prod.yaml build --no-cache
[+] Building 320.7s (25/25) FINISHED                                                                                                                                                        docker:desktop-linux
 => [php internal] load build definition from Dockerfile                                                                                                                                                    0.0s
 => => transferring dockerfile: 3.13kB                                                                                                                                                                      0.0s
 => [php] resolve image config for docker-image://docker.io/docker/dockerfile:1                                                                                                                             0.4s
 => CACHED [php] docker-image://docker.io/docker/dockerfile:1@sha256:4c68376a702446fc3c79af22de146a148bc3367e73c25a5803d453b6b3f722fb                                                                       0.0s
 => [php internal] load metadata for docker.io/dunglas/frankenphp:1-php8.3                                                                                                                                  0.4s
 => [php internal] load .dockerignore                                                                                                                                                                       0.0s
 => => transferring context: 498B                                                                                                                                                                           0.0s
 => [php internal] load build context                                                                                                                                                                       0.0s
 => => transferring context: 18.42kB                                                                                                                                                                        0.0s
 => [php frankenphp_upstream 1/1] FROM docker.io/dunglas/frankenphp:1-php8.3@sha256:80a0db5e0b3bec1c93067407fe0a9047e6af41202d5e476c0a3c2101238ce4f6                                                        0.0s
 => CACHED [php frankenphp_base 1/7] WORKDIR /app                                                                                                                                                           0.0s
 => [php frankenphp_base 2/7] RUN apt-get update && apt-get install -y --no-install-recommends     acl     file     gettext     git     curl     && rm -rf /var/lib/apt/lists/*                            35.1s
 => [php frankenphp_base 3/7] RUN set -eux;     install-php-extensions         @composer         apcu         intl         opcache         zip         gd         zlib     ;                              174.6s
 => [php frankenphp_base 4/7] RUN install-php-extensions pdo_pgsql                                                                                                                                         26.3s
 => [php frankenphp_base 5/7] COPY --link frankenphp/conf.d/10-app.ini /usr/local/etc/php/app.conf.d/                                                                                                       0.0s
 => [php frankenphp_base 6/7] COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint                                                                                      0.0s
 => [php frankenphp_base 7/7] COPY --link frankenphp/Caddyfile /etc/caddy/Caddyfile                                                                                                                         0.0s
 => [php frankenphp_prod 1/9] RUN mv "/usr/local/etc/php/php.ini-production" "/usr/local/etc/php/php.ini"                                                                                                   0.2s
 => [php frankenphp_prod 2/9] COPY --link frankenphp/conf.d/20-app.prod.ini /usr/local/etc/php/app.conf.d/                                                                                                  0.0s
 => [php frankenphp_prod 3/9] COPY --link frankenphp/worker.Caddyfile /etc/caddy/worker.Caddyfile                                                                                                           0.0s
 => [php frankenphp_prod 4/9] COPY --link composer.* symfony.* ./                                                                                                                                           0.0s
 => [php frankenphp_prod 5/9] RUN set -eux;     composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress                                                              14.8s
 => [php frankenphp_prod 6/9] COPY --link . ./                                                                                                                                                              0.1s
 => [php frankenphp_prod 7/9] RUN rm -Rf frankenphp/                                                                                                                                                        0.2s
 => [php frankenphp_prod 8/9] RUN set -eux;     mkdir -p var/cache var/log;     composer dump-autoload --classmap-authoritative --no-dev;     composer dump-env prod;     composer run-script --no-dev pos  3.2s
 => [php frankenphp_prod 9/9] RUN apt-get update &&     curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash &&     export NVM_DIR="/config/nvm" &&     [ -s "$NVM_DIR/nvm.sh"  62.0s
 => [php] exporting to image                                                                                                                                                                                3.0s
 => => exporting layers                                                                                                                                                                                     3.0s
 => => writing image sha256:d5d3d7d2d3bc202b4658844da2517cffa5d5c4bf536dc6d77046b676ccfdd1d1                                                                                                                0.0s
 => => naming to docker.io/library/app-php                                                                                                                                                                  0.0s
 => [php] resolving provenance for metadata file                                                                                                                                                            0.0s
[+] Building 1/1
 ✔ php  Built
```

You can see that `frankenphp_base` and `frankenphp_prod` are built this time, which is what you will need for production purposes.

You can finally start your prod container(s) by running: `docker compose -f compose.yaml -f compose.prod.yaml up -d`.
Careful: the order of `-f` arguments is important.

</details>

