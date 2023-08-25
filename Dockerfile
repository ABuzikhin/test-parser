FROM php:8.1-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt update

RUN pecl install xdebug-3.1.5
RUN docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    git \
    nodejs \
    npm
RUN docker-php-ext-install zip mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

RUN npm install --global yarn

WORKDIR /var/www/app