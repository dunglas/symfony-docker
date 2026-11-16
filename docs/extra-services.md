# Support for Extra Services

Symfony Docker is extensible.
When you install a compatible Composer package using Symfony Flex,
the recipe will automatically modify the `Dockerfile` and `compose.yaml` files
to fulfill the requirements of this package.

The currently supported packages are:

- `symfony/orm-pack`: install a PostgreSQL service
- `symfony/mercure-bundle`: use the Mercure.rocks module shipped with Caddy
- `symfony/panther`: install Chromium and its drivers
- `symfony/mailer`: install a Mailpit service
- `blackfireio/blackfire-symfony-meta`: install a Blackfire service

> [!NOTE]
>
> If a recipe modifies the Dockerfile, the container needs to be rebuilt.

<!-- -->

> [!WARNING]
>
> We recommend that you use the `composer require` command inside the container
> in development mode so that recipes can be applied correctly.

## Using Another Email Catcher

The `symfony/mailer` recipe installs Mailpit.
Any other SMTP catcher works in the same place.
To use [Mailtrap Local](https://github.com/mailtrap/mailtrap-local) instead,
replace the `mailer` service in `compose.override.yaml`:

```yaml
mailer:
  image: mailtrap/mailtrap-local
  ports:
    - "3535"
    - "3550"
```

Mailtrap Local needs no environment variables.
The recipe sets `MAILER_DSN=null://null`, so change that line in `.env`:

```dotenv
MAILER_DSN=smtp://mailer:3535
```

Port 3550 serves both the web interface and the JSON API.
Neither port is published on a fixed host port.
Run `docker compose port mailer 3550` to get the web interface address.
