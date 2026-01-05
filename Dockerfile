# =========================
# Symfony 7 + PostgreSQL Dockerfile fixe
# =========================

FROM php:8.2-apache

# -------------------------
# Définir le dossier public Symfony
# -------------------------
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# -------------------------
# Désactiver TOUS les MPM puis activer mpm_prefork
# -------------------------
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
 && a2enmod mpm_prefork

# Adapter la config Apache pour le dossier public/
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
# Copier projet Symfony
# -------------------------
WORKDIR /var/www/html
COPY . .

# -------------------------
# Installer Composer
# -------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------
# Installer dépendances Symfony
# -------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------
# Permissions Apache
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
