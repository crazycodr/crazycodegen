FROM php:8.3

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

COPY . /app

WORKDIR /app