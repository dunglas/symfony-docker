# Installing Xdebug

The default Docker stack is shipped without [Xdebug](https://xdebug.org/),
a popular debugger and profiler for PHP.
It's easy, though, to add it to your project.

## Add a Debug Stage to the Dockerfile

To avoid deploying Symfony Docker to production with an active Xdebug extension,
it's recommended to add a custom stage to the end of the `Dockerfile`.

```Dockerfile
# Dockerfile
FROM symfony_php AS symfony_php_debug

ARG XDEBUG_VERSION=3.1.2
RUN set -eux; \
	apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
	pecl install xdebug-$XDEBUG_VERSION; \
	docker-php-ext-enable xdebug; \
	apk del .build-deps
```

## Configure Xdebug with Docker Compose Override

Using an [override](https://docs.docker.com/compose/reference/overview/#specifying-multiple-compose-files) file named `docker-compose.debug.yml` ensures that the production
configuration remains untouched.

As an example, an override could look like this:

```yaml
# docker-compose.debug.yml
version: "3.4"

services:
  php:
    build:
      context: .
      target: symfony_php_debug
    environment:
      # See https://docs.docker.com/docker-for-mac/networking/#i-want-to-connect-from-a-container-to-a-service-on-the-host
      # See https://github.com/docker/for-linux/issues/264
      # The `client_host` below may optionally be replaced with `discover_client_host=yes`
      # Add `start_with_request=yes` to start debug session on each request
      XDEBUG_CONFIG: >-
        client_host=host.docker.internal
      XDEBUG_MODE: debug
      # This should correspond to the server declared in PHPStorm `Preferences | Languages & Frameworks | PHP | Servers`
      # Then PHPStorm will use the corresponding path mappings
      PHP_IDE_CONFIG: serverName=symfony
    extra_hosts:
      # Ensure that host.docker.internal is correctly defined on Linux
      - host.docker.internal:host-gateway
```

Build your image with your fresh new XDebug configuration:

```console
docker-compose -f docker-compose.yml -f docker-compose.debug.yml build
```

Then run:

```console
docker-compose -f docker-compose.yml -f docker-compose.debug.yml up -d
```

## Debugging with Xdebug and PHPStorm

You can use the **Xdebug extension** for [Chrome](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc) or [Firefox](https://addons.mozilla.org/fr/firefox/addon/xdebug-helper-for-firefox/) if you want to debug on the browser (don't forget to configure it).

If you don't want to use it, add on your request this query param: `XDEBUG_SESSION=PHPSTORM`.

On PHPStorm, click on `Start Listening for PHP Debug Connections` in the `Run` menu.

Otherwise, you can create a [PHP Remote Debug](https://www.jetbrains.com/help/phpstorm/creating-a-php-debug-server-configuration.html) configuration with the following parameters:

* Server:
  * Name: `symfony` (must be the same as defined in `PHP_IDE_CONFIG`)
  * Host: `https://localhost` (or the one defined with `SERVER_NAME`)
  * Port: `443`
  * Debugger: `Xdebug`
  * Absolute path on the server: `/srv/app`
* IDE key: `PHPSTORM`

You can now use the debugger.

## Troubleshooting

Inspect the installation with the following command. The Xdebug version should be displayed.

```console
$ docker-compose exec php php --version

PHP ...
    with Xdebug v3.1.2 ...
```
