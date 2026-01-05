FROM php:8.2-cli

# Dépendances système
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /app

# Copier le projet
COPY . .

# Installer dépendances Symfony
RUN composer install --no-dev --optimize-autoloader

# Exposer le port Railway
EXPOSE 8080

# Lancer Symfony
CMD php -S 0.0.0.0:$PORT -t public
