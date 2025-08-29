# Support for Extra Services

Symfony Docker is extensible. When you install a compatible Composer package using Symfony Flex,
the recipe will automatically modify the `Dockerfile` and `compose.yaml` to fulfill the requirements of this package.

The currently supported packages are:

-   `symfony/orm-pack`: install a PostgreSQL service
-   `symfony/mercure-bundle`: use the Mercure.rocks module shipped with Caddy
-   `symfony/panther`: install chromium and these drivers
-   `symfony/mailer`: install a Mailpit service
-   `blackfireio/blackfire-symfony-meta`: install a Blackfire service

> [!NOTE]
> If a recipe modifies the Dockerfile, the container needs to be rebuilt.

> [!WARNING]
> We recommend that you use the `composer require` command inside the container in development mode so that recipes can be applied correctly
