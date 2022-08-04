# Build Options

## Selecting a Specific Symfony Version

Use the `SYMFONY_VERSION` environment variable to select a specific Symfony version.

For instance, use the following command to install Symfony 5.4:

    SYMFONY_VERSION=5.4.* docker compose up --build

## Installing Development Versions of Symfony

To install a non-stable version of Symfony, use the `STABILITY` environment variable during the build.
The value must be [a valid Composer stability option](https://getcomposer.org/doc/04-schema.md#minimum-stability)) .

For instance, use the following command to use the development branch of Symfony:

    STABILITY=dev docker compose up --build

## Customizing the Server Name

Use the `SERVER_NAME` environment variable to define your custom server name(s).

    SERVER_NAME="app.localhost, caddy:80" docker compose up --build

If you use Mercure, keep `caddy:80` in the list to allow the PHP container to request the caddy service.

## Using custom HTTP ports

Use the environment variables `HTTP_PORT`, `HTTPS_PORT` and/or `HTTP3_PORT` to adjust the ports to your needs, e.g.

    HTTP_PORT=8000 HTTPS_PORT=4443 HTTP3_PORT=4443 docker compose up --build

to access your appplication on [https://localhost:4443](https://localhost:4443).
