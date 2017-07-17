#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
    # The first time volumes are mounted, dependencies need to be reinstalled
    if [ ! -f composer.json ]; then
        rm -Rf vendor/*
        php -r "copy('$SKELETON_COMPOSER_JSON', 'composer.json');"
        composer install --prefer-dist --no-progress --no-suggest --no-interaction
    fi

	# Permissions hack because setfacl does not work on Mac and Windows
	chown -R www-data var
fi

exec docker-php-entrypoint "$@"
