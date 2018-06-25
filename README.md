Symfony docker
======

Usage
------

Run `docker-compose up -d`

Choosing symfony version
------

To choose specific symfony version define `SYMFONY_VERSION` env variable for first run.

Like:

`SYMFONY_VERSION=3.4 docker-compose up -d`

To install symfony with different stability (more about stability [here](https://getcomposer.org/doc/04-schema.md#minimum-stability)) define `STABILITY` env variable for first run.

Like:

`STABILITY=dev docker-compose up -d`

will install symfony from master branch.
