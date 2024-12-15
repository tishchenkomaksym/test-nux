FROM php:8.3-fpm

RUN apt-get update && apt-get install -y build-essential
RUN docker-php-ext-install  opcache pdo pdo_mysql
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN docker-php-ext-install exif && docker-php-ext-enable exif
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apt-get update && apt-get install zip unzip
RUN docker-php-ext-install bcmath

WORKDIR /var/www/html

#make install && make deploy
