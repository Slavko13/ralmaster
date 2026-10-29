# syntax=docker/dockerfile:1.7

FROM php:8.2.28-apache-bookworm AS runtime

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        default-mysql-client \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        curl \
        exif \
        gd \
        intl \
        mbstring \
        mysqli \
        opcache \
        pdo_mysql \
        zip \
    && a2enmod expires headers rewrite \
    && rm -rf /var/lib/apt/lists/*

RUN install -d -m 0755 /opt/ralmaster

COPY --chmod=0644 docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY --chmod=0644 docker/php/bitrix.ini /usr/local/etc/php/conf.d/90-bitrix.ini
COPY --chmod=0644 docker/healthz.php /opt/ralmaster/healthz.php

WORKDIR /var/www/html

HEALTHCHECK --interval=10s --timeout=5s --start-period=30s --retries=6 \
    CMD curl --fail --silent http://127.0.0.1/healthz.php >/dev/null || exit 1

FROM runtime AS development
COPY --chmod=0644 docker/php/development.ini /usr/local/etc/php/conf.d/99-environment.ini

FROM runtime AS production
COPY --chmod=0644 docker/php/production.ini /usr/local/etc/php/conf.d/99-environment.ini
