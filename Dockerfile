# Dockerfile to prepare a local PHP code execution environment.
FROM php:8.1

WORKDIR /tmp

# You need curl and zip to work webdriver client.
RUN apt-get update && \
    apt-get install -y bash curl libzip-dev git zlib1g-dev&& \
    docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN composer self-update

COPY composer.json .
RUN composer install
COPY . .
