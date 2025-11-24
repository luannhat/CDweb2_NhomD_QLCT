# Base image PHP + Apache
FROM php:8.2-apache

# Cài đặt các package cần thiết cho GD
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài extension PHP cần cho MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files vào container để cài đặt dependencies
COPY composer.json composer.lock* /var/www/html/

# Cài đặt mPDF và các dependencies
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy toàn bộ mã nguồn vào container (sau khi cài dependencies)
COPY . /var/www/html/

# Mở cổng 80 cho web
EXPOSE 80
