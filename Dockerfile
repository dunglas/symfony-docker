FROM composer:1.6
FROM php:7.2-fpm-alpine

RUN apk add --no-cache --virtual .persistent-deps \
		git \
		icu-libs \
		zlib

ENV APCU_VERSION 5.1.11
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

COPY docker/app/php.ini /usr/local/etc/php/php.ini
COPY --from=0 /usr/bin/composer /usr/bin/composer
COPY docker/app/docker-entrypoint.sh /usr/local/bin/docker-app-entrypoint
RUN chmod +x /usr/local/bin/docker-app-entrypoint

WORKDIR /srv/app
ENTRYPOINT ["docker-app-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

# Use prestissimo to speed up builds
RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative  --no-interaction

# Allow to use development versions of Symfony
ARG STABILITY=stable
ENV STABILITY ${STABILITY}

# Download the Symfony skeleton and leverage Docker cache layers
RUN composer create-project "symfony/skeleton" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-scripts --no-plugins --no-interaction

###> recipes ###
###< recipes ###

COPY . .

RUN mkdir -p var/cache var/logs var/sessions \
    && composer install --prefer-dist --no-dev --no-progress --no-suggest --classmap-authoritative --no-interaction \
	&& composer clear-cache \
	&& chown -R www-data var
