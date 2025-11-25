#!/usr/bin/env bash

# Cập nhật package list
sudo apt-get update -y

# Cài Apache, PHP, MySQL
sudo apt-get install -y apache2 php php-mysql libapache2-mod-php mysql-server

# Bật mod_rewrite cho Laravel hoặc routing PHP
sudo a2enmod rewrite

# Trỏ DocumentRoot về /vagrant/public (thay vì /var/www/html)
sudo rm -rf /var/www/html
sudo ln -fs /vagrant/public /var/www/html

# Khởi động lại Apache
sudo systemctl restart apache2

echo "✅ Provisioning complete! Environment ready."
