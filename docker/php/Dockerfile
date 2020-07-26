FROM php:7.4-fpm

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zlib1g-dev \
    libicu-dev

RUN pecl install \
    apcu

RUN docker-php-ext-install \
    pdo_mysql \
    zip \
    opcache

RUN docker-php-ext-enable \
    apcu

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN PATH=$PATH:/var/www/vendor/bin:bin

CMD ["php-fpm"]

EXPOSE 9000