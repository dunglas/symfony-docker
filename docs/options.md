# Docker Build Options

You can customize the docker build process using these environment variables.

> [!NOTE]  
> All Symfony-specific environment variables are used only if no `composer.json` file is found in the project directory. 

## Selecting a Specific Symfony Version

Use the `SYMFONY_VERSION` environment variable to select a specific Symfony version.

For instance, use the following command to install Symfony 6.4:

On Linux:

    SYMFONY_VERSION=6.4.* docker compose up --wait
On Windows:

    set SYMFONY_VERSION=6.4.* && docker compose up --wait&set SYMFONY_VERSION=

## Installing Development Versions of Symfony

To install a non-stable version of Symfony, use the `STABILITY` environment variable during the build.
The value must be [a valid Composer stability option](https://getcomposer.org/doc/04-schema.md#minimum-stability).

For instance, use the following command to use the development branch of Symfony:

On Linux:

    STABILITY=dev docker compose up --wait

On Windows:
    
    set STABILITY=dev && docker compose up --wait&set STABILITY=

## Using custom HTTP ports

Use the environment variables `HTTP_PORT`, `HTTPS_PORT` and/or `HTTP3_PORT` to adjust the ports to your needs, e.g.

    HTTP_PORT=8000 HTTPS_PORT=4443 HTTP3_PORT=4443 docker compose up --wait

to access your application on [https://localhost:4443](https://localhost:4443).

> [!NOTE]  
> Let's Encrypt only supports the standard HTTP and HTTPS ports. Creating a Let's Encrypt certificate for another port will not work, you have to use the standard ports or to configure Caddy to use another provider.


## Caddyfile Options

You can also customize the `Caddyfile` by using the following environment variables to inject options block, directive or configuration.

> [!TIP]  
> All the following environment variables can be defined in your `.env` file at the root of the project to keep them persistent at each startup

| Environment variable            | Description                                                                                                                                                                             | Default value             |
|---------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------|
| `CADDY_GLOBAL_OPTIONS`          | the [global options block](https://caddyserver.com/docs/caddyfile/options#global-options), one per line                                                                                 |                           |
| `CADDY_EXTRA_CONFIG`            | the [snippet](https://caddyserver.com/docs/caddyfile/concepts#snippets) or the [named-routes](https://caddyserver.com/docs/caddyfile/concepts#named-routes) options block, one per line |                           |
| `CADDY_SERVER_EXTRA_DIRECTIVES` | the [`Caddyfile` directives](https://caddyserver.com/docs/caddyfile/concepts#directives)                                                                                                |                           |
| `CADDY_SERVER_LOG_OPTIONS`      | the [server log options block](https://caddyserver.com/docs/caddyfile/directives/log), one per line                                                                                     |                           |
| `SERVER_NAME`                   | the server name or address                                                                                                                                                              | `localhost`               |
| `FRANKENPHP_CONFIG`             | a list of extra [FrankenPHP directives](https://frankenphp.dev/docs/config/#caddyfile-config), one per line                                                                             | `import worker.Caddyfile` | 
| `MERCURE_TRANSPORT_URL`         | the value passed to the `transport_url` directive                                                                                                                                       | `bolt:///data/mercure.db` |
| `MERCURE_PUBLISHER_JWT_KEY`     | the JWT key to use for publishers                                                                                                                                                       |                           |
| `MERCURE_PUBLISHER_JWT_ALG`     | the JWT algorithm to use for publishers                                                                                                                                                 | `HS256`                   |
| `MERCURE_SUBSCRIBER_JWT_KEY`    | the JWT key to use for subscribers                                                                                                                                                      |                           |
| `MERCURE_SUBSCRIBER_JWT_ALG`    | the JWT algorithm to use for subscribers                                                                                                                                                | `HS256`                   |
| `MERCURE_EXTRA_DIRECTIVES`      | a list of extra [Mercure directives](https://mercure.rocks/docs/hub/config), one per line                                                                                               |                           |

### Example of server name customize:

    SERVER_NAME="app.localhost" docker compose up --wait
