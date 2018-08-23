# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework.

## Getting Started

1. Run `docker-compose up` (the logs will be displayed in the current shell)
2. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
3. **Enjoy!**

## Selecting a Specific Symfony Version

Use the `SYMFONY_VERSION` environment variable to select a specific Symfony version.

For instance, use the following command to install Symfony 3.4:

`SYMFONY_VERSION=3.4 docker-compose up --build`

To install a non-stable version of Symfony, use the `STABILITY` environment variable during the build.
The value must be a valid [a valid Composer stability option](https://getcomposer.org/doc/04-schema.md#minimum-stability)) .

For instance, use the following command to use the `master` branch of Symfony:

`STABILITY=dev docker-compose up --build`

## Credits

Created by [Kévin Dunglas](https://dunglas.fr) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
