FROM php:8.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git curl unzip \
    default-mysql-client \
    zlib1g-dev libzip-dev \
    && docker-php-ext-install pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY php.ini /usr/local/etc/php/

WORKDIR /var/www