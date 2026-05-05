# Base image: PHP 8.2 with Apache web server built in
FROM php:8.2-apache

# Install the mysqli extension so PHP can talk to MySQL
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite (needed if you add pretty URLs later)
RUN a2enmod rewrite

# Copy all project files into the Apache web root inside the container
COPY . /var/www/html/

# Create the uploads folder and give Apache write permission
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads

# Apache listens on port 80 inside the container
EXPOSE 80
