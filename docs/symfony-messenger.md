# Using async Symfony Messenger

Add new service to the `compose.yaml`:
```yaml
  php-worker:
    image: ${IMAGES_PREFIX:-}app-php-worker
    restart: unless-stopped
    environment:
      - RUN_MIGRATIONS=false
    healthcheck:
      disable: true
    depends_on:
      - php
```

Add new services to the `compose.override.yaml`:
```yaml
  php-worker:
    profiles:
      - donotstart

  php-worker-async:
    scale: 2
    extends:
      file: compose.yaml
      service: php-worker
    image: ${IMAGES_PREFIX:-}app-php-worker-async
    build:
      context: .
      target: frankenphp_dev
    command: ['bin/console', 'messenger:consume', 'async', '-vv', '--time-limit=60', '--limit=10', '--memory-limit=128M']
    volumes:
      - ./:/app
      - /app/var/
    depends_on:
      php:
        condition: service_healthy
```

Two instances, which are defined by `scale` property, of `php-worker-async` will start after `php` container which does installation of Symfony and runs database migrations. They will share app folder because of `- ./:/app` in volumes configuration. `- /app/var/` defines that every container will have its own and separate `/app/var/` folder, [note missing `:`](https://stackoverflow.com/questions/46166304/docker-compose-volumes-without-colon).

To add additional workers just copy `php-worker-async` service and replace every usage of the `async` in the new service with appropriate value for the new worker.

In `compose.yaml` we have base `php-worker` service which is extended by `php-worker-async` in `compose.override.yaml` and in `compose.prod.yaml` with `extends` property. [`donotstart` service profile](https://docs.docker.com/compose/how-tos/profiles/) on `php-worker` service is used to prevent it from starting, because it is only used as a base for eventual multiple workers with different configuration.

Add new services to the `compose.prod.yaml`:
```yaml
  php-worker:
    profiles:
      - donotstart

  php-worker-async:
    scale: 2
    extends:
      file: compose.yaml
      service: php-worker
    image: ${IMAGES_PREFIX:-}app-php-worker-async
    build:
      context: .
      target: frankenphp_prod
    command: ['bin/console', 'messenger:consume', 'async', '-vv', '--time-limit=60', '--limit=10', '--memory-limit=128M']
    depends_on:
      php:
        condition: service_healthy
```

Apply the following changes to the `frankenphp/docker-entrypoint.sh`:
```patch
-	if grep -q ^DATABASE_URL= .env; then
+	if grep -q ^DATABASE_URL= .env && [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
```

> [!NOTE]
> After all changes are made the containers need to be rebuilt.
