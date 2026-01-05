FROM php:8.2-apache

# Symfony public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Désactiver tous les MPM et activer mpm_prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
 && rm -f /etc/apache2/mods-enabled/mpm_*.conf \
 && a2enmod mpm_prefork

# Extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_pgsql

# Copier projet
WORKDIR /var/www/html
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer dépendances (prod)
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data var vendor

# Exposer le port Apache
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
