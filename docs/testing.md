# Running tests

Since the `APP_ENV` value hardcoded as an environment value in the php container,
running `composer dump-env test` will not override this. Therefore, it is not natively possible
to run tests in the `test` environment when using your development container(s).

## I want to run tests on my server

Simple: you only need to set up and deploy you project the same way as for the dev environment,
but you will need to set APP_ENV to test.

You can set this value in `compose.override.yaml` directly, or provide your environment values
with a specific `env-file`:

```bash
docker compose --env-file <your-env-file> up --wait
```

## I want to run tests on my desktop

On your desktop, your container is most likely running in the `dev` environment.
And since `APP_ENV` is hardcoded as one of the container environment values,
you won't be able to override it.
So if you try to use `php bin/phpunit`, you will execute your tests in the
`dev` environment. That's not ideal for unit tests, and clearly bad if you run
tests against your database.

The solution is to use a Makefile
(https://symfony.com/doc/current/the-fast-track/en/17-tests.html#automating-your-workflow-with-a-makefile).
You can start from this template:

```makefile
SHELL := /bin/bash
CONTAINER_EXEC := docker compose exec -i -e APP_ENV=test <your_dev_container> php

tests:
	$(CONTAINER_EXEC) bin/console doctrine:database:drop --force || true
	$(CONTAINER_EXEC) bin/console doctrine:database:create
	$(CONTAINER_EXEC) bin/console doctrine:migrations:migrate -n
	$(CONTAINER_EXEC) bin/console doctrine:fixtures:load -n
	$(CONTAINER_EXEC) bin/phpunit $(MAKECMDGOALS)
.PHONY: tests
```

That way, you can actually override the `APP_ENV` value to `test`.

> [!NOTE]
>
> Be mindful about your databases names and/or the value of `APP_DATABASE`:
> if you use the same for both dev and test environment, execution of fixtures
> and tests will break your dev database.
> Using `when@test.doctrine.dbal.dbname_suffix: "_test"` is a possible solution
> to have both databases in the database container.
