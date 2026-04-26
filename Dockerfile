# Use the official PHP image with Apache web server
FROM php:8.2-apache

# Install MySQLi extension for database connections
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy your PHP project files
COPY . /var/www/html/

# Update permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80