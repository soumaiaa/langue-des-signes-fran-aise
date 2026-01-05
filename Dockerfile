FROM php:8.2-apache
ARG CACHE_BUST=1

# 🔴 FIX MPM OBLIGATOIRE
RUN a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork

# Apache → Symfony public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Extensions PHP
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git \
 && docker-php-ext-install pdo pdo_pgsql \
 && a2enmod rewrite \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*

# Projet Symfony
WORKDIR /var/www/html
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 🚨 IMPORTANT : désactiver scripts auto
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Créer var si absent + permissions
RUN mkdir -p var


EXPOSE 80
CMD ["apache2-foreground"]
