# syntax=docker/dockerfile:1

########################################
# Stage 0: PHP base (FrankenPHP + composer + extensions)
########################################
FROM dunglas/frankenphp:php8.4 AS php-base

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions \
        pcntl \
        exif \
        pdo_mysql \
        redis \
        zip

########################################
# Stage 1: PHP dependencies (vendor)
########################################
FROM php-base AS composer-vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts

########################################
# Stage 2: frontend assets (Vite)
########################################
FROM node:22-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# Vite build imports vendor JS and scans vendor views via Tailwind @source.
COPY --from=composer-vendor /app/vendor ./vendor

# VITE_* values are baked into the JS bundle at build time.
ARG VITE_APP_NAME
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME
ENV VITE_APP_NAME=$VITE_APP_NAME \
    VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY \
    VITE_REVERB_HOST=$VITE_REVERB_HOST \
    VITE_REVERB_PORT=$VITE_REVERB_PORT \
    VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

RUN npm run build

########################################
# Stage 3: PHP runtime (FrankenPHP/Octane)
########################################
FROM php-base

RUN apt-get update && apt-get install -y --no-install-recommends \
        default-mysql-client \
        curl \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /etc/mysql/conf.d \
    && printf '[client]\nssl-verify-server-cert=0\n' > /etc/mysql/conf.d/zz-client-ssl.cnf

WORKDIR /app

COPY . /app

COPY --from=composer-vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build

RUN mkdir -p storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             storage/app/public \
             storage/app/private \
    && php artisan package:discover --ansi

COPY Scripts/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]
CMD ["php", "artisan", "octane:frankenphp"]
