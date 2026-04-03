# 1. Build the laravel application dependencies using the official Composer image as a base.
# each FROM is a fresh file system, so we need to copy the application code and dependencies from the previous stages to the final image.
FROM composer:2 AS composer_deps

# here /app refers to the working directory inside the container, not the host machine. This is where the application code and dependencies will be copied and installed.
WORKDIR /app

# Copy the composer files and install the dependencies
COPY composer.json composer.lock ./ 
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


# 2. Build the frontend assets using the official Node.js image as a base.    
FROM node:22-alpine AS frontend_builder

WORKDIR /app

# Copy the package files
COPY package.json package-lock.json ./
# Install the frontend dependencies
RUN npm ci

# Copy the frontend source code
COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY package.json ./
# Build the frontend assets 
RUN npm run build


# 3. Build the final application image using the official PHP image as a base, and copy the dependencies and frontend assets from the previous stages.
FROM php:8.3-fpm-bullseye AS app

# Set the working directory to /var/www/html, which is the default document root for the PHP-FPM image.
WORKDIR /var/www/html

# Install the necessary PHP extensions and system dependencies, and clean up the apt cache to reduce the image size.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
        libicu-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-install \
        bcmath \
        intl \
        pcntl \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

# vendor is the default directory for Laravel dependencies, so we copy the installed dependencies from the composer_deps stage to the vendor directory in the final image.
COPY --from=composer_deps /app/vendor ./vendor
COPY . .
# Copy the built frontend assets from the previous stage to the public/build directory, which is where Laravel Mix outputs the compiled assets.
COPY --from=frontend_builder /app/public/build ./public/build

# copy the custom entrypoint scripts for starting PHP-FPM and the queue worker, and make them executable.
COPY docker/start-fpm.sh /usr/local/bin/start-fpm
COPY docker/start-queue.sh /usr/local/bin/start-queue

# Set the appropriate permissions for the storage and cache directories, and remove any existing cached files to ensure a clean state when the container starts.
RUN chmod +x /usr/local/bin/start-fpm /usr/local/bin/start-queue \
    && rm -f bootstrap/cache/*.php \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000


# 4. Build the final Nginx image, and copy the frontend assets and Nginx configuration from the previous stages.
FROM nginx:1.27-alpine AS nginx

WORKDIR /var/www/html

COPY public ./public
COPY --from=frontend_builder /app/public/build ./public/build
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
