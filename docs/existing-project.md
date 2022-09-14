# Installing on an Existing Project

It's also possible to use Symfony Docker with existing projects!

First, [download this skeleton](https://github.com/dunglas/symfony-docker). If you clone the Git repository, be sure to remove the `.git` directory to prevent conflicts with the `.git` directory already in your existing project.

Then, copy the Docker-related files from the skeleton to your existing project:

    cp -Rp symfony-docker/. my-existing-project/

Enable the Docker support of Symfony Flex:

    composer config --json extra.symfony.docker 'true'

Re-execute the recipes to update the Docker-related files according to the packages you use

    rm symfony.lock
    composer symfony:sync-recipes --force --verbose

Double-check the changes, revert the changes that you don't want to keep:

    git diff
    ...

Build the Docker images:

    docker-compose build --no-cache --pull

Start the project!

    docker-compose up -d

Browse `https://localhost`, your Docker configuration is ready!
