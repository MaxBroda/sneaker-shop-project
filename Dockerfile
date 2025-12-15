FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
  libsqlite3-dev \
  unzip \
  && docker-php-ext-install pdo pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite headers

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY ./backend /var/www/html

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
