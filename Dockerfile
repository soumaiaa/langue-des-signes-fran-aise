FROM php:8.2-cli

# Extensions PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git \
 && docker-php-ext-install pdo pdo_pgsql \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Dossier app
WORKDIR /app
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer dépendances prod (sans scripts)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Créer var
RUN mkdir -p var

# Port Railway
EXPOSE 8080

# Démarrer Symfony
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
