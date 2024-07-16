# Use an official PHP runtime as a parent image
FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql mysqli

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy composer.json and composer.lock if they exist
COPY composer.json composer.lock ./

# Install project dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the application code
COPY . .

# Run composer scripts (autoload, etc.)
RUN composer dump-autoload --optimize --no-scripts

# Expose ports
EXPOSE 80

# Start Apache service
CMD ["apache2-foreground"]