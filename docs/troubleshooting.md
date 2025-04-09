# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## TLS/HTTPS Issues

See more in the [TLS section](tls.md)

## Production issues

### How to properly build fresh images for production use

Remember that, by default, if you run `docker compose up --wait`, only the files `compose.yaml` and `compose.override.yaml` will be used.
See https://docs.docker.com/compose/intro/compose-application-model and https://docs.docker.com/compose/how-tos/multiple-compose-files/merge.

If you need to build images for production environment, you have to use the following command:

```console
docker compose -f compose.yaml -f compose.prod.yaml build --pull --no-cache
```

### Why application outputs `phpinfo()`

Both dev and prod images have the same image tag (`<...>app-php:latest`). This can cause confusion when working with images.
It is important to make sure that your image is the appropriate one for the current environment.

If you are not careful about this, and try to run your production container(s) with
`docker compose -f compose.yaml -f compose.prod.yaml up --wait`
without the right build process beforehand, your application **will still launch**, but will be displaying an output of `phpinfo()` (or possibly even a HTTP 500 error page).

See details below.

<details>

<summary>Output of a basic build process</summary>

In the case of a dev image, you need the `compose.yaml` and `compose.override.yaml` files. Which are the default files for Docker Compose.
This means that running `docker compose <command>` or `docker compose -f compose.yaml -f compose.override.yaml <command>` is the same thing.

And in doing so, images `frankenphp_base` and `frankenphp_dev` are built. And not `frankenphp_prod`.
Which is good enough for dev purposes.

Then, you can start your dev container(s) by running:

```console
docker compose up --wait
```


</details>

<br>

<details>

<summary>Output expected for the production build process</summary>

To build the production image, you <ins>have to</ins> specify the `compose.yaml` and `compose.prod.yaml` files.
This means you have to run: `docker compose -f compose.yaml -f compose.prod.yaml build --pull --no-cache` in order to build your image
(careful: the order of `-f` arguments is important).

That way, you will see that `frankenphp_base` and `frankenphp_prod` are built this time, which is what you will need for production purposes.

You can finally start your prod container(s) by running:

```console
docker compose -f compose.yaml -f compose.prod.yaml up --wait
```

</details>

