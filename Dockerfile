FROM php:8.3

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

COPY . /app
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app