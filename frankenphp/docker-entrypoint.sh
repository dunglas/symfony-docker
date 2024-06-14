#!/bin/sh
set -e

if [ "$1" = 'frankenphp' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	# Install the project the first time PHP is started
	# After the installation, the following block can be deleted
	if [ ! -f composer.json ]; then
		rm -Rf tmp/
		composer create-project "laravel/laravel $LARAVEL_VERSION" tmp --stability="$STABILITY" --prefer-dist --no-progress --no-interaction

		cd tmp
		cp -Rp . ..
		cd -
		rm -Rf tmp/
	fi

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX storage bootstrap/cache
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX storage bootstrap/cache
fi

exec docker-php-entrypoint "$@"
