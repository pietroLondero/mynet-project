FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd mbstring zip

RUN a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY symfony/ /app/

WORKDIR /app

RUN useradd -m symfony && chown -R symfony:symfony /app
USER symfony
RUN composer install --no-scripts --no-interaction --optimize-autoloader

USER root

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
