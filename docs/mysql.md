# Using MySQL

The Docker configuration in this repository defaults to using PostgreSQL.
If you prefer to work with MySQL, some adjustments need to be made.

First, install the doctrine/orm pack as described: `make composer c='req symfony/orm-pack`

## Docker configuration
Change the database image to use MySQL instead of PostgreSQL in `compose.yaml`:

```yaml
###> doctrine/doctrine-bundle ###
  database:
    image: 'mysql:8.0'
    environment:
      - MYSQL_ROOT_PASSWORD=r007_p4ssw0rd
      - MYSQL_DATABASE=databasename
      - MYSQL_USER=mysqluser
      - MYSQL_PASSWORD=p4ssw0rd
    volumes:
      - database_data:/var/lib/mysql:rw
###< doctrine/doctrine-bundle ###
```
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

Last but not least, we need to install the mysql driver in `Dockerfile`:
```diff
###> doctrine/doctrine-bundle ###
- RUN install-php-extensions pdo_postgres
+ RUN install-php-extensions pdo_mysql
###< doctrine/doctrine-bundle ###
```

## Change environment
```dotenv 
DATABASE_URL="mysql://mysqluser:p4ssw0rd@database:3306/databasename?serverVersion=8.0.32&charset=utf8mb4"
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
