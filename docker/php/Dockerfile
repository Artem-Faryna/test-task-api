FROM php:8.0.6-fpm

RUN apt-get update && apt-get install -y \
        git \
        curl \
        wget \
        && docker-php-ext-install pdo_mysql \
        && pecl install xdebug-3.0.4 \
        && docker-php-ext-enable xdebug

ADD php.ini /usr/local/etc/php/php.ini

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /usr/src/app
USER 1000:1000
CMD ["php-fpm"]
