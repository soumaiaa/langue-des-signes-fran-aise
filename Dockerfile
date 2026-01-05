# =========================
# Dockerfile prêt pour Symfony 7 + PostgreSQL sur Railway
# =========================

# Base image PHP + Apache
FROM php:8.2-apache

# -------------------------
# Définir le dossier public Symfony
# -------------------------
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Adapter la config Apache pour Symfony public/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# -------------------------
# Installer les extensions PHP nécessaires
# -------------------------
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_pgsql \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# -------------------------
# Copier le projet Symfony
# -------------------------
WORKDIR /var/www/html
COPY . .

# -------------------------
# Installer Composer
# -------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------
# Installer les dépendances Symfony en prod
# -------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------
# Permissions pour Apache
# -------------------------
RUN chown -R www-data:www-data var vendor

# -------------------------
# Exposer le port Apache
# -------------------------
EXPOSE 80

# -------------------------
# Lancer Apache (commande standard)
# -------------------------
CMD ["apache2-foreground"]
