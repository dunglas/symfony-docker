# Build Options

## Selecting a Specific Symfony Version

Use the `SYMFONY_VERSION` environment variable to select a specific Symfony version.

For instance, use the following command to install Symfony 4.4:

    $ SYMFONY_VERSION=4.4.* docker-compose up --build

To install a non-stable version of Symfony, use the `STABILITY` environment variable during the build.
The value must be [a valid Composer stability option](https://getcomposer.org/doc/04-schema.md#minimum-stability)) .

For instance, use the following command to use the `master` branch of Symfony:

    $ STABILITY=dev docker-compose up --build

## Customizing the Server Name

Use the `SERVER_NAME` environment variable to define your custom server name(s).

    $ SERVER_NAME="symfony.wip, caddy:80" docker-compose up --build

If you use Mercure, keep `caddy:80` in the list to allow the PHP container to request the caddy service.
