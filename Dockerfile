# Gunakan base image PHP + Apache
FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Copy source code ke dalam container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Set permission untuk folder Laravel
RUN chmod -R 775 storage bootstrap/cache

# Expose port 10000 (Render pakai port ini)
EXPOSE 10000

# Jalankan Laravel dengan PHP built-in server
CMD php artisan serve --host=0.0.0.0 --port=10000
