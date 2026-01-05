# -------------------------
# Base image PHP + Apache
# -------------------------
FROM php:8.2-apache

# -------------------------
# Définir le dossier public Symfony
# -------------------------
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Appliquer la config Symfony au document root Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# -------------------------
# Désactiver les MPM multiples pour Apache
# -------------------------
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
 && rm -f /etc/apache2/mods-enabled/mpm_*.conf \
 && a2enmod mpm_prefork

# -------------------------
# Installer les extensions nécessaires
# -------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_pgsql

# -------------------------
# Copier le projet
# -------------------------
WORKDIR /var/www/html
COPY . .

# -------------------------
# Installer Composer
# -------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------
# Installer les dépendances PHP
# -------------------------
# Utiliser --no-dev uniquement pour prod
ARG APP_ENV=prod
RUN if [ "$APP_ENV" = "prod" ]; then \
        composer install --no-dev --optimize-autoloader; \
    else \
        composer install --no-interaction --optimize-autoloader; \
    fi

# -------------------------
# Permissions
# -------------------------
RUN chown -R www-data:www-data var vendor

# -------------------------
# Exposer le port Apache
# -------------------------
EXPOSE 80

# -------------------------
# Lancer Apache
# -------------------------
CMD ["apache2-foreground"]
