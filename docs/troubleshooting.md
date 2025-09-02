# Troubleshooting

## Editing Permissions on Linux

If you work on Linux and cannot edit some of the project files right after
the first installation, you can run the following command
to set yourself as owner of the project files that were created by the Docker container:

```console
docker compose run --rm php chown -R $(id -u):$(id -g) .
```

## TLS/HTTPS Issues

See more in the [TLS section](tls.md)

## Production Issues

### How To Properly Build Fresh Images for Production Use

Remember that, by default, if you run `docker compose up --wait`,
only the files `compose.yaml` and `compose.override.yaml` will be used.
See ["How Compose works"](https://docs.docker.com/compose/intro/compose-application-model)
and ["Merge Compose files"](https://docs.docker.com/compose/how-tos/multiple-compose-files/merge).

If you need to build images for production environment, you have to use the following
command:

```console
docker compose -f compose.yaml -f compose.prod.yaml build --pull --no-cache
```

### Why Application Outputs `phpinfo()`?

Both dev and prod images have the same image tag (`<...>app-php:latest`).
This can cause confusion when working with images.
It is important to make sure that your image is the appropriate one
for the current environment.

If you are not careful about this, and try to run your production container(s) with
`docker compose -f compose.yaml -f compose.prod.yaml up --wait`
without the right build process beforehand, your application **will still launch**,
but will be displaying an output of `phpinfo()`
(or possibly even a HTTP 500 error page).

See details below.

#### Output of a basic build process

In the case of a dev image, you need the `compose.yaml` and
`compose.override.yaml` files, which are the default files for Docker Compose.

This means that running `docker compose <command>` or
`docker compose -f compose.yaml -f compose.override.yaml <command>` is the same thing.

In doing so, images `frankenphp_base` and `frankenphp_dev` are built,
but not `frankenphp_prod`, which is good enough for dev purposes.

Then, you can start your dev container(s) by running:

```console
docker compose up --wait
```

#### Output expected for the production build process

To build the production image, you have to specify the `compose.yaml` and
`compose.prod.yaml` files.

This means you have to run the following command in order to build your image:

```console
docker compose -f compose.yaml -f compose.prod.yaml build --pull --no-cache
```

> [!WARNING]
>
> The order of `-f` arguments matters.

That way, you will see that `frankenphp_base` and `frankenphp_prod` are built,
which is what you will need for production purposes.

You can finally start your prod container(s) by running:

```console
docker compose -f compose.yaml -f compose.prod.yaml up --wait
```
