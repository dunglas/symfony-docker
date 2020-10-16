# Installing Xdebug

The default Docker stack is shipped without a Xdebug stage.
It's easy though to add [Xdebug](https://xdebug.org/) to your project, for development purposes such as debugging tests or API requests remotely.

## Add a Debug Stage to the Dockerfile

To avoid deploying Symfony Docker to production with an active Xdebug extension,
it's recommended to add a custom stage to the end of the `Dockerfile`.

```Dockerfile
# Dockerfile
FROM symfony_php as symfony_php_debug

ARG XDEBUG_VERSION=2.9.8
RUN set -eux; \
	apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
	pecl install xdebug-$XDEBUG_VERSION; \
	docker-php-ext-enable xdebug; \
	apk del .build-deps
```

## Configure Xdebug with Docker Compose Override

Using an [override](https://docs.docker.com/compose/reference/overview/#specifying-multiple-compose-files) file named `docker-compose.debug.yaml` ensures that the production
configuration remains untouched.

As example, an override could look like this:

```yaml
# docker-compose.debug.yaml
version: "3.4"

services:
  php:
    build:
      context: .
      target: symfony_php_debug
    environment:
      # See https://docs.docker.com/docker-for-mac/networking/#i-want-to-connect-from-a-container-to-a-service-on-the-host
      # See https://github.com/docker/for-linux/issues/264
      # The `remote_host` below may optionally be replaced with `remote_connect_back`
      XDEBUG_CONFIG: >-
        remote_enable=1
        remote_host=host.docker.internal
        remote_port=9001
        idekey=PHPSTORM
      # This should correspond to the server declared in PHPStorm `Preferences | Languages & Frameworks | PHP | Servers`
      # Then PHPStorm will use the corresponding path mappings
      PHP_IDE_CONFIG: serverName=symfony
```

Then run:

    $ docker-compose -f docker-compose.yml -f docker-compose.debug.yml up -d

## Troubleshooting

Inspect the installation with the following command. The requested Xdebug version should be displayed in the output.

    $ docker-compose exec php php --version
    
    PHP ...
        with Xdebug v2.8.0 ...
