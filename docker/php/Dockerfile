FROM php:8.1.9-fpm-alpine
#ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

#RUN apk add wget git unzip zlib-dev libpng-dev libzip-dev libpq-dev icu-dev iputils autoconf gcc g++ make linux-headers unixodbc-dev gnupg openssh-keygen
#RUN docker-php-ext-install pdo pdo_mysql bcmath zip gd intl sockets pcntl
#RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
#    install-php-extensions imagick

COPY ./php/xdebug.ini /usr/local/etc/php/conf.d
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/