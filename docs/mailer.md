# Sending Emails

## In Development: Catching Emails with Mailpit

When you install [Symfony Mailer](https://symfony.com/doc/current/mailer.html),
the Symfony Flex recipe automatically registers a [Mailpit](https://mailpit.axllent.org/)
service in `compose.override.yaml`.

Mailpit acts as a fake SMTP server: it catches every email sent by your
application and displays it in a web UI, so no real email is ever delivered
during development.

### Configuring the DSN

The recipe leaves `MAILER_DSN=null://null` (emails are discarded), so point the
Mailer to the `mailer` service in `.env` (or `.env.local`):

```dotenv
MAILER_DSN=smtp://mailer:1025
```

> [!WARNING]
> Use the Compose service name (`mailer`), **not** `localhost` or `127.0.0.1`.
> The PHP application runs in its own container, so `127.0.0.1` refers to the
> PHP container itself, not to the Mailpit container. Using
> `smtp://127.0.0.1:1025` results in a "Connection refused" error.

### Accessing the Web UI

By default, the recipe exposes Mailpit's ports without publishing them to
fixed ports on the host. To find the random port assigned to the web UI, run:

```console
docker compose port mailer 8025
```

Alternatively, publish a fixed port by editing the service definition in
`compose.override.yaml`:

```yaml
ports:
  - "1025"
  - "8025:8025"
```

The web UI is then available on `http://localhost:8025`.

### Testing

Send a test email from inside the PHP container:

```console
docker compose exec php bin/console mailer:test you@example.com
```

The email appears instantly in the Mailpit web UI.

## In Production

The Mailpit service is defined in `compose.override.yaml` only, which is not
used in production (see the [deployment documentation](production.md)).
Consequently, nothing intercepts emails in production: you must configure a
real transport.

Set the `MAILER_DSN` environment variable to point to your SMTP server or
email provider when starting the stack:

```console
MAILER_DSN=smtp://user:pass@smtp.example.com:587 \
SERVER_NAME=your-domain-name.example.com \
APP_SECRET=ChangeMe \
CADDY_MERCURE_JWT_SECRET=ChangeThisMercureHubJWTSecretKey \
docker compose -f compose.yaml -f compose.prod.yaml up --wait
```

Symfony Mailer also provides [third-party transports](https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport)
(Mailtrap, Amazon SES, Brevo, Mailgun, Postmark, Resend...) installable as
separate Composer packages, which are usually more reliable than a raw SMTP
connection.

> [!TIP]
> If emails are sent asynchronously through Messenger, remember that they are
> delivered by the worker consuming the `async` transport, not by the web
> container. Make sure the worker has access to the same `MAILER_DSN`.

## On Staging Environments

Reusing Mailpit on a staging server is a cheap safety net: by pointing the
staging `MAILER_DSN` to a Mailpit instance, you can test your application
with production-like data without any risk of sending real emails to real
users, while still being able to inspect every message in the web UI.
