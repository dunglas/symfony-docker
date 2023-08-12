# Laravel Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Laravel](https://laravel.com/) web framework,
with full HTTP/2 and HTTPS support.

Full inspired by [Symfony Docker](https://github.com/dunglas/symfony-docker/blob/main/README.md).

## Getting Started

1. [Install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build` to build fresh images
3. Run `docker compose up --pull --wait` to start the project, add custom options for custom start
4. Open `https://localhost` on web browser
   and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334).
5. Run `docker compose down --remove-orphans` to stop Docker containers.

## Features

* Production, development and CI ready
* Automatic HTTPS (in dev and in prod!)
* [XDebug](https://xdebug.org/) support
* Just 2 services (PHP FPM and Nginx server)

## License

Laravel Docker is available under the MIT License.
