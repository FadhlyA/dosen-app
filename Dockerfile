# Laravel 10 + FrankenPHP + PHP 8.2 + Vite 5
# Designed for Railway deployment.

FROM dunglas/frankenphp:php8.2-bookworm

ARG NODE_VERSION=20.19.4

WORKDIR /app

# System packages + PHP build/runtime dependencies.
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    git \
    unzip \
    zip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions commonly required by this Laravel application and its packages.
RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    intl \
    mbstring \
    pcntl \
    pdo_mysql \
    zip

# Composer: copy from the official Composer image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js 20 for Vite 5.
RUN curl -fsSL https://nodejs.org/dist/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64.tar.xz \
    -o /tmp/node.tar.xz \
    && tar -xJf /tmp/node.tar.xz -C /usr/local --strip-components=1 \
    && rm /tmp/node.tar.xz \
    && node --version \
    && npm --version

# Copy dependency manifests first for better Docker layer caching.
COPY composer.json composer.lock package.json package-lock.json ./

# Production PHP dependencies.
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Frontend dependencies. package-lock.json is used when present.
RUN npm ci

# Copy the application.
COPY . .

# Build Vite assets, then remove node_modules from the final image.
RUN npm run build \
    && rm -rf node_modules

# Laravel runtime directories and permissions.
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

# Run Laravel package discovery after the application and vendor are present.
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

# Railway supplies PORT. FrankenPHP listens on this value at runtime.
ENV SERVER_NAME=":${PORT:-8080}"

EXPOSE 8080

# Laravel/FrankenPHP entrypoint.
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
