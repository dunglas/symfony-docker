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

The [worker mode of FrankenPHP](https://frankenphp.dev/docs/worker/) is enabled by default.
To use it with Symfony ≤ 7.3, install the FrankenPHP runtime:

```console
composer require runtime/frankenphp-symfony
```

> [!TIP]
>
> You can disable worker mode by removing the `worker` directive from the `frankenphp`
> global option in your `Caddyfile`.

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
