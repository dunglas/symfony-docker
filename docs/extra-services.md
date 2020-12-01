# Support for Extra Services

Symfony Docker is extensible. When you install a compatible Composer package using Symfony Flex,
the recipe will automatically modify the `Dockerfile` and `docker-compose.yml` to fulfill the requirements of this package.

The currently supported packages are:

* `symfony/orm-pack`: install a PostgreSQL service
* `symfony/mercure-bundle`: use the Mercure.rocks module shipped with Caddy
* `symfony/messenger`: install a RabbitMQ service
