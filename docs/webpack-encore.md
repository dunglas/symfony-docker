# Using with Webpack Encore

First, install Webpack Encore.
```shell
composer require symfony/webpack-encore-bundle
```

Modify `Dockerfile` to build your assets :

- At the top of the file before anything else
```diff
+ARG NODE_VERSION=19
```
- Replace the prod image for an intermediary step by renaming it
```diff
-# Prod image
-FROM php:8.2-fpm-alpine AS app_php
+# Composer stage
+FROM php:8.2-fpm-alpine AS app_composer
```
- Install node modules and build assets, then copy them to the prod image.

```diff
# copy sources
COPY --link  . ./
RUN rm -Rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log; \
    if [ -f composer.json ]; then \
		composer dump-autoload --classmap-authoritative --no-dev; \
		composer dump-env prod; \
		composer run-script --no-dev post-install-cmd; \
		chmod +x bin/console; sync; \
    fi

+# Node stage
+FROM node:${NODE_VERSION}-alpine AS symfony_node
+
+COPY --link --from=app_composer /srv/app/package*.json /app/
+COPY --link --from=app_composer /srv/app/vendor /app/vendor
+
+WORKDIR /app
+
+RUN npm install --force
+
+COPY --link --from=app_composer /srv/app/assets /app/assets
+COPY --link --from=app_composer /srv/app/webpack.config.js /app/
+
+RUN npm run build
+
+# Prod image
+FROM app_composer as app_php
+
+COPY --from=symfony_node --link /app/public/build /srv/app/public/build/

# Dev image
FROM app_php AS app_php_dev
```

Modify `docker-compose.override.yml` to add a `node` container in your development environment.
This will provide you with hot module reloading.

```diff
  caddy:
    volumes:
      - ./docker/caddy/Caddyfile:/etc/caddy/Caddyfile:ro
      - ./public:/srv/app/public:ro

+  node:
+    build:
+      context: .
+      target: symfony_node
+    volumes:
+      - ./:/app
+    ports:
+     - target: 8080
+       published: 8080
+       protocol: tcp
+    command: 'sh -c "npm install;
+      npm run dev-server -- --live-reload --server-type https --client-web-socket-url https://localhost:8080/ws --host 0.0.0.0 --public https://localhost:8080"'
```

If file changes are not picked up, refer to this page:
https://symfony.com/doc/current/frontend/encore/virtual-machine.html#file-watching-issues
