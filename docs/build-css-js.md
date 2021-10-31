# Building CSS and JS

For security reason and limit the size of the container, the default stack doesn't include any development "artifacts".
To add it to the project and your development workflow follow this guide.

*This example uses `npm` and [Webpack Encore](https://symfony.com/doc/current/frontend.html).*

Modify `Dockerfile` to build your assets :

- At the top of the file before anything else
```diff
ARG PHP_VERSION=8.1
ARG CADDY_VERSION=2
+ARG NODE_VERSION=18
+
+# node "stage"
+FROM node:${NODE_VERSION}-alpine AS symfony_node
+
+WORKDIR /srv/app
+
+COPY package*.json ./
+
+RUN npm install
+## If you are building your code for production
+# RUN npm ci --only=production
+
+## You need to copy everything to use PostCSS, Tailwinds, ... 
+COPY . .
+
+RUN npm run build

# "php" stage
```

- Then copy the built output in `public/build` (the configuration default) to the `php` container (which will then be copied later to `caddy`).

```diff
VOLUME /srv/app/var

+COPY --from=symfony_node /srv/app/public/build public/build

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM caddy:${CADDY_VERSION}-builder-alpine AS symfony_caddy_builder
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
+      - ./:/srv/app
+    ports:
+     - target: 8080
+       published: 8080
+       protocol: tcp
+    command: 'sh -c "npm install; npm run dev-server -- --server-type https --client-web-socket-url https://localhost:8080 --host 0.0.0.0"'
```

If file changes are not picked up, refer to this page:
https://symfony.com/doc/current/frontend/encore/virtual-machine.html#file-watching-issues
