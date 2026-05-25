# syntax=docker/dockerfile:1

###############################################################################
# Stage 1 — Composer dependencies (no dev packages, optimized autoloader)
###############################################################################
FROM composer:2 AS vendor

WORKDIR /app

# Copy only the files needed to resolve & install dependencies first so this
# layer is cached as long as composer.json / composer.lock don't change.
COPY composer.json composer.lock ./

# Install without running artisan scripts (the full app isn't copied yet) and
# without dev dependencies. Autoloader is optimized for production.
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --no-progress

# Now copy the rest of the application and build the optimized autoloader.
COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative


###############################################################################
# Stage 2 — Production runtime (PHP 8.3 FPM)
#
# This is a pure JSON API (no Blade views / frontend assets), so there is no
# Vite/Node build step — only PHP code and the production vendor directory.
###############################################################################
FROM php:8.3-fpm-bookworm AS app

# ---- System dependencies -----------------------------------------------------
# poppler-utils provides the `pdftotext` binary required by spatie/pdf-to-text.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        poppler-utils \
    && rm -rf /var/lib/apt/lists/*

# ---- PHP extensions ----------------------------------------------------------
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        gd \
        opcache \
        pcntl

# Redis extension (used when QUEUE_CONNECTION / CACHE_STORE = redis).
RUN pecl install redis && docker-php-ext-enable redis

# ---- PHP configuration -------------------------------------------------------
COPY docker/php/php.ini      /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/opcache.ini  /usr/local/etc/php/conf.d/zz-opcache.ini

WORKDIR /var/www/html

# ---- Application code --------------------------------------------------------
# Bring in the app + production vendor dir.
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor

# Ensure writable runtime dirs exist and are owned by the FPM user.
RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ---- Entrypoint --------------------------------------------------------------
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 9000

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
