FROM php:8.2-apache

# instala ffmpeg, extensões PHP, utilitários
RUN apt-get update && \
    apt-get install -y ffmpeg libzip-dev unzip git && \
    docker-php-ext-install gd mbstring

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

# roda o arquivo composer.json da raiz
RUN composer install --no-interaction --prefer-dist --optimize-autoloader || true

RUN a2enmod rewrite

EXPOSE 80
