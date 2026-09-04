FROM php:8.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends git libicu-dev libsqlite3-dev libzip-dev unzip \
    && docker-php-ext-install intl pcntl pdo_mysql pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/zz-app.conf /usr/local/etc/php-fpm.d/zz-app.conf

WORKDIR /var/www/html
