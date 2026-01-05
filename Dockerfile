FROM php:8.2-apache

# Apache → dossier public Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_pgsql

# Activer rewrite
RUN a2enmod rewrite

# Copier projet
WORKDIR /var/www/html
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer dépendances PROD uniquement
RUN composer install --no-dev --optimize-autoloader

# Permissions Symfony
RUN chown -R www-data:www-data var vendor

EXPOSE 80
CMD ["apache2-foreground"]
