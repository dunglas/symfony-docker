# Installing on an Existing Project

It's also possible to use Laravel Docker with existing projects!

First, [download this skeleton](https://github.com/FunkyOz/laravel-docker). If you clone the Git repository, be sure to remove the `.git` directory to prevent conflicts with the `.git` directory already in your existing project.

Then, copy the Docker-related files from the skeleton to your existing project:

    cp -Rp laravel-docker/. my-existing-project/

Double-check the changes, revert the changes that you don't want to keep:

    git diff
    ...

Build the Docker images:

    docker compose build --no-cache --pull

Start the project!

    docker compose up -d

Browse `https://localhost`, your Docker configuration is ready!
