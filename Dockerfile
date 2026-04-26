# Use the official PHP image with Apache web server
FROM php:8.2-apache

# Enable Apache mod_rewrite (often needed for routing)
RUN a2enmod rewrite

# Copy your PHP project files into the server's public folder
COPY . /var/www/html/

# Update permissions so Apache can read the files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80