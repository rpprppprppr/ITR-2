FROM php:8.4-cli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev

#Установка xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

#Установка mysql
RUN docker-php-ext-install pdo pdo_mysql