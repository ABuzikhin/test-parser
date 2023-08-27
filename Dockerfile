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
    npm \
    wget \
    gnupg \
    chromium

    # for backward compatibility, make both path be symlinked to the binary.
RUN ln -s /usr/bin/chromium /usr/bin/google-chrome-unstable
RUN ln -s /usr/bin/chromium /usr/bin/google-chrome

RUN docker-php-ext-install zip mysqli pdo pdo_mysql sockets && docker-php-ext-enable pdo_mysql sockets

RUN npm install --global yarn

WORKDIR /var/www/app