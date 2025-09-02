# Makefile

Here is a Makefile template. It provides some shortcuts for the most common tasks.
To use it, create a new `Makefile` file at the root of your project. Copy/paste
the content in the template section. To view all the available commands, run `make`.

For example, in the [getting started section](/README.md#getting-started), the
`docker compose` commands could be replaced by:

1. Run `make build` to build fresh images
2. Run `make up` (detached mode without logs)
3. Run `make down` to stop the Docker containers

Of course, this template is basic for now. But, as your application is growing,
you will probably want to add some targets like running your tests as described
in [the Symfony book](https://symfony.com/doc/current/the-fast-track/en/17-tests.html#automating-your-workflow-with-a-makefile).
You can also find a more complete example in this [snippet](https://www.strangebuzz.com/en/snippets/the-perfect-makefile-for-symfony).

If you want to run make from within the `php` container, in the [Dockerfile](/Dockerfile),
add:

<!-- markdownlint-disable MD010 -->

```diff
 	gettext \
 	git \
+	make \
```

<!-- markdownlint-enable MD010 -->

And rebuild the PHP image.

> [!NOTE]
>
> If you are using Windows, you have to install [chocolatey.org](https://chocolatey.org/)
> or [Cygwin](http://cygwin.com) to use the `make` command.
>
> Check out this [StackOverflow question](https://stackoverflow.com/q/2532234/633864)
> for more explanations.

## The Template

<!-- markdownlint-disable MD010 MD013 -->

```Makefile
# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)


## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf
```

<!-- markdownlint-enable MD010 MD013 -->

## Adding and Modifying Jobs

Make sure to add this configuration to the [.editorconfig](/.editorconfig) file,
so that it forces your editor to use tabs instead of spaces.

> [!NOTE]
>
> Makefiles are not compatible with spaces by default.

```.editorconfig

[Makefile]
indent_style = tab

```

If you still want to use space, you can configure the prefix in the Makefile itself.
See [this answer on Stack Exchange](https://retrocomputing.stackexchange.com/a/20303).
