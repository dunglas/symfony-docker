#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'artisan' ]; then
	# Install the project the first time PHP is started
	# After the installation, the following block can be deleted
	if [ ! -f composer.json ]; then
	    echo "Download app at tmp/"
		rm -Rf tmp/
		composer create-project "laravel/laravel $LARAVEL_VERSION" tmp --stability="$STABILITY" --prefer-dist --no-progress --no-interaction --no-install --no-scripts

        echo "Copy app in root directory"
		cd tmp
		cp -Rp . ..
		cd -

        echo "Remove tmp/"
		rm -Rf tmp/

        echo "Run scripts"
        composer run-script post-root-package-install
		composer install --prefer-dist --no-progress --no-interaction
		composer run-script post-create-project-cmd
    elif [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi
fi

exec docker-php-entrypoint "$@"
