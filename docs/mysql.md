# Using MySQL

The Docker configuration of this repository is extensible thanks to Flex recipes. By default, the recipe installs PostgreSQL.
If you prefer to work with MySQL, follow these steps:

First, install the doctrine/orm pack as described: `make composer c='req symfony/orm-pack`

## Docker Configuration
Change the database image to use MySQL instead of PostgreSQL in `compose.yaml`:

```diff
###> doctrine/doctrine-bundle ###
-   image: postgres:${POSTGRES_VERSION:-15}-alpine
+   image: mysql:${MYSQL_VERSION:-8}
    environment:
-     POSTGRES_DB: ${POSTGRES_DB:-app}
+     MYSQL_DATABASE: ${MYSQL_DATABASE:-app}
      # You should definitely change the password in production
+     MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-!ChangeMe!}
-     POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:-!ChangeMe!}
+     MYSQL_PASSWORD: ${MYSQL_PASSWORD:-!ChangeMe!}
-     POSTGRES_USER: ${POSTGRES_USER:-app}
+     MYSQL_USER: ${MYSQL_USER:-app}
    volumes:
-     - database_data:/var/lib/postgresql/data:rw
+     - database_data:/var/lib/mysql:rw
      # You may use a bind-mounted host directory instead, so that it is harder to accidentally remove the volume and lose all your data!
-     # - ./docker/db/data:/var/lib/postgresql/data:rw
+     # - ./docker/db/data:/var/lib/mysql:rw
###< doctrine/doctrine-bundle ###
Depending on the database configuration, modify the environment in the same file at `services.php.environment.DATABASE_URL`
```
DATABASE_URL: mysql://mysqluser:p4ssw0rd@database:3306/nucast?serverVersion=8.0.32&charset=utf8mb4
```

Since we changed the port, we also have to define this in the `compose.override.yaml`:
```yaml
###> doctrine/doctrine-bundle ###
  database:
    ports:
      - "3306:3306"
###< doctrine/doctrine-bundle ###
```

Last but not least, we need to install the MySQL driver in `Dockerfile`:
```diff
###> doctrine/doctrine-bundle ###
-RUN install-php-extensions pdo_pgsql
+RUN install-php-extensions pdo_mysql
###< doctrine/doctrine-bundle ###
```

## Change Environment
```dotenv 
DATABASE_URL=mysql://${MYSQL_USER:-app}:${MYSQL_PASSWORD:-!ChangeMe!}@database:3306/${MYSQL_DATABASE:-app}?serverVersion=${MYSQL_VERSION:-8}&charset=${MYSQL_CHARSET:-utf8mb4}
```

## Final steps
Rebuild the docker environment:
```shell
make down && make build
```

Test your setup:
```shell
make sf c='dbal:run-sql -q "SELECT 1" && echo "OK" || echo "Connection is not working"'`
```
