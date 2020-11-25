#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
	if [ "$APP_ENV" != 'prod' ]; then
		PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
	fi
	ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"

  mkdir -p var/cache var/log

  # The first time volumes are mounted, the project needs to be recreated
  if [ ! -f composer.json ]; then
      composer create-project "symfony/skeleton $SYMFONY_VERSION" tmp --stability=$STABILITY --prefer-dist --no-progress --no-interaction
      jq '.extra.symfony.docker=true' tmp/composer.json > tmp/composer.tmp.json
      rm tmp/composer.json
      mv tmp/composer.tmp.json tmp/composer.json

      cp -Rp tmp/. .
      rm -Rf tmp/
  elif [ "$APP_ENV" != 'prod' ]; then
      rm -f .env.local.php
      composer install --prefer-dist --no-progress --no-interaction
  fi

  if grep -q DATABASE_URL= .env; then
    echo "Waiting for db to be ready..."
    ATTEMPTS_LEFT_TO_REACH_DATABASE=60
    until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
      sleep 1
      ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE-1))
      echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
    done

    if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
      echo "The db is not up or not reachable"
      exit 1
    else
       echo "The db is now ready and reachable"
    fi

    if ls -A migrations/*.php > /dev/null 2>&1; then
      bin/console doctrine:migrations:migrate --no-interaction
    fi
  fi

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
