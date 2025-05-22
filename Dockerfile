FROM php:8.2-fpm

# Installer les dépendances nécessaires pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    unzip \
    libzip-dev

# https://github.com/mlocati/docker-php-extension-installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo pdo_pgsql zip xdebug

# si l'app était censé allé en prod il aurait fallu faire une image séparée avec et sans xdebug
COPY php/conf.d/app.dev.ini /usr/local/etc/php/conf.d/app.dev.ini
ENV PHP_IDE_CONFIG="serverName=parser-app"

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Nettoyer le cache apt pour alléger l'image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
