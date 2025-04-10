# Installing on an Existing Project

It's also possible to use Symfony Docker with existing projects!

First, [download this skeleton](https://github.com/dunglas/symfony-docker).

If you cloned the Git repository, be sure to not copy the `.git` directory to prevent conflicts with the `.git` directory already in your existing project.
You can copy the contents of the repository using git and tar. This will not contain `.git` or any uncommited changes.

    git archive --format=tar HEAD | tar -xC my-existing-project/

If you downloaded the skeleton as a zip you can just copy the extracted files:

    cp -Rp symfony-docker/. my-existing-project/

Enable the Docker support of Symfony Flex:

    composer config --json extra.symfony.docker 'true'

Re-execute the recipes to update the Docker-related files according to the packages you use

    rm symfony.lock
    composer recipes:install --force --verbose

Double-check the changes, revert the changes that you don't want to keep:

    git diff
    ...

Build the Docker images:

    docker compose build --pull --no-cache

Start the project!

    docker compose up --wait

Browse `https://localhost`, your Docker configuration is ready!

> [!NOTE]
> If you want to use the worker mode of FrankenPHP, make sure you required the `runtime/frankenphp-symfony` package.

> [!NOTE]
> The worker mode of FrankenPHP is enabled by default in prod. To disabled it, add the env var FRANKENPHP_CONFIG as empty to the compose.prod.yaml file.
