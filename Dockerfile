# syntax=docker/dockerfile:1

# ============================================================
# Stage 1 — Composer dependencies (levert vendor/, incl. Flux CSS)
# ============================================================
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# ============================================================
# Stage 2 — Frontend assets bouwen (heeft vendor/ nodig voor flux.css)
# ============================================================
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ============================================================
# Stage 3 — PHP-FPM runtime (Laravel 13, PHP 8.4)
# ============================================================
FROM php:8.4-fpm-alpine AS app
WORKDIR /var/www/html

RUN apk add --no-cache \
        bash git icu-dev libzip-dev oniguruma-dev mysql-client \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath intl opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# --no-scripts: package:discover draait niet tijdens build (geen .env/APP_KEY),
# Laravel ontdekt packages bij de eerste start.
RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh
