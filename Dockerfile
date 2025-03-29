# Use official PHP image with Apache
FROM php:8.1-apache

# Enable mod_rewrite for URL routing
RUN a2enmod rewrite

# Install MySQL extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy project files into the container
COPY ./public /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80
EXPOSE 80
