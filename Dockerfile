FROM php:7.1-fpm-alpine

RUN apk add --no-cache --virtual .persistent-deps \
		git \
		icu-libs \
		make \
		zlib

ENV APCU_VERSION 5.1.8

RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		zlib-dev \
	&& docker-php-ext-install \
		intl \
		zip \
	&& pecl install \
		apcu-${APCU_VERSION} \
	&& docker-php-ext-enable --ini-name 20-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache \
	&& apk del .build-deps

###> recipes ###
###< recipes ###

COPY docker/app/php.ini /usr/local/etc/php/php.ini

COPY docker/app/install-composer.sh /usr/local/bin/docker-app-install-composer
RUN chmod +x /usr/local/bin/docker-app-install-composer

RUN set -xe \
	&& docker-app-install-composer \
	&& mv composer.phar /usr/local/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative \
	&& composer clear-cache

WORKDIR /srv/app

COPY . .
# Cleanup unneeded files
RUN rm -Rf docker/

# Download the Symfony skeleton
ENV SKELETON_COMPOSER_JSON https://raw.githubusercontent.com/symfony/skeleton/v3.3.2/composer.json
RUN [ -f composer.json ] || php -r "copy('$SKELETON_COMPOSER_JSON', 'composer.json');"

RUN mkdir -p var/cache var/logs var/sessions \
    && composer install --prefer-dist --no-dev --no-progress --no-suggest --optimize-autoloader --classmap-authoritative --no-interaction \
	&& composer clear-cache \
# Permissions hack because setfacl does not work on Mac and Windows
	&& chown -R www-data var

COPY docker/app/docker-entrypoint.sh /usr/local/bin/docker-app-entrypoint
RUN chmod +x /usr/local/bin/docker-app-entrypoint

ENTRYPOINT ["docker-app-entrypoint"]
CMD ["php-fpm"]
