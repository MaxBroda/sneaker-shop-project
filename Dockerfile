FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
  libsqlite3-dev \
  && docker-php-ext-install pdo pdo_sqlite

RUN a2enmod rewrite headers

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY ./backend /var/www/html

EXPOSE 80
