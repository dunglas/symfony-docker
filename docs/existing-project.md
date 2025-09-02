# Installing on an Existing Project

It's also possible to use Symfony Docker with existing projects!

First, [download this skeleton](https://github.com/dunglas/symfony-docker).

If you cloned the Git repository, be sure to not copy the `.git` directory
to prevent conflicts with the `.git` directory already in your existing project.

You can copy the contents of the repository using Git and tar.
This will not contain `.git` or any uncommited changes.

```console
git archive --format=tar HEAD | tar -xC my-existing-project/
```

If you downloaded the skeleton as a ZIP you can just copy the extracted files:

```console
cp -Rp symfony-docker/. my-existing-project/
```

Enable the Docker support of Symfony Flex:

```console
composer config --json extra.symfony.docker 'true'
```

If you want to use the [worker mode of FrankenPHP](https://github.com/php/frankenphp/blob/main/docs/worker.md),
add the FrankenPHP runtime for Symfony:

```console
composer require runtime/frankenphp-symfony
```

> [!TIP]
>
> With Symfony 7.4, the `runtime/frankenphp-symfony` package isn't required anymore,
> as Symfony Runtime natively supports FrankenPHP worker mode.

Re-execute the recipes to update the Docker-related files according to
the packages you use:

```console
rm symfony.lock
composer recipes:install --force --verbose
```

Double-check the changes, revert the changes that you don't want to keep:

```console
git diff
```

Build the Docker images:

```console
docker compose build --pull --no-cache
```

Start the project!

```console
docker compose up --wait
```

Browse `https://localhost`, your Docker configuration is ready!

> [!NOTE]
>
> The worker mode of FrankenPHP is enabled by default in the Caddyfile.
> To disabled it, comment the `worker {}` section of this file.
>
> You may also remove `runtime/frankenphp-symfony`
> if you never plan on using the worker mode.
