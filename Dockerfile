# Utiliser PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Copier le projet dans le container
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Donner les droits sur var et vendor
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

# Exposer le port 80
EXPOSE 80

# Lancer Apache en foreground
CMD ["apache2-foreground"]
