# Build Options

## Selecting a Specific Laravel Version

Use the `LARAVEL_VERSION` environment variable to select a specific Laravel version.

For instance, use the following command to install Laravel 9:

On Linux:

    LARAVEL_VERSION=9 docker compose up --wait

On Windows:

    set LARAVEL_VERSION=9&& docker compose up --wait&set LARAVEL_VERSION=

## Installing Development Versions of Laravel

To install a non-stable version of Laravel, use the `STABILITY` environment variable during the build.
The value must be [a valid Composer stability option](https://getcomposer.org/doc/04-schema.md#minimum-stability).

For instance, use the following command to use the development branch of Laravel:

On Linux:

    STABILITY=dev docker compose up --wait

On Windows:

    set STABILITY=dev&& docker compose up --wait&set STABILITY=

## Customizing the Server Name

Use the `SERVER_NAME` environment variable to define your custom server name(s).

    SERVER_NAME="app.localhost" docker compose up --wait

*Tips: You can define your server name variable in your `.env` file to keep it at each up*

## Using custom HTTP ports

Use the environment variables `HTTP_PORT` and/or `HTTPS_PORT` to adjust the ports to your needs, e.g.

    HTTP_PORT=8000 HTTPS_PORT=4443 docker compose up --wait

to access your application on [https://localhost:4443](https://localhost:4443).
