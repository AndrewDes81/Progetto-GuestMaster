FROM php:8.2-apache

# Installa le dipendenze di sistema e le estensioni per PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Abilita il modulo rewrite di Apache per gli URL puliti (opzionale ma utile)
RUN a2enmod rewrite