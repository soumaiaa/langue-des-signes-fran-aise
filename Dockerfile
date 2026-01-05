FROM php:8.2-apache

# Désactiver TOUS les MPM
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
 && rm -f /etc/apache2/mods-enabled/mpm_*.conf

# Activer UN SEUL MPM compatible PHP
RUN a2enmod mpm_prefork

# Définir le dossier public Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Installer extensions PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_pgsql

# Copier le projet
WORKDIR /var/www/html
COPY . .

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer dépendances Symfony
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data var vendor
