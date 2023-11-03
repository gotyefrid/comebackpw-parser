FROM php:8.1.9-fpm-alpine
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

# Для установки xdebug нужны вот эти зависимости
RUN apk add autoconf gcc g++ make

RUN pecl install xdebug-3.1.2
RUN docker-php-ext-enable xdebug
COPY ./php/xdebug.ini /usr/local/etc/php/conf.d
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/