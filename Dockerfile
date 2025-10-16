# Base image PHP + Apache
FROM php:8.2-apache

# Cài extension PHP cần cho MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy toàn bộ mã nguồn vào container
COPY . /var/www/html/

# Mở cổng 80 cho web
EXPOSE 80
