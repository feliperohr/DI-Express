FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y unzip git libzip-dev && \
    docker-php-ext-install zip

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

RUN composer install --no-interaction --prefer-dist --optimize-autoloader || true

RUN a2enmod rewrite

EXPOSE 80
