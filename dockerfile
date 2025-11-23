FROM php:8.2-apache

# Install mysqli and pdo_mysql extensions for MySQL support
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your project into the container
COPY . /var/www/html

WORKDIR /var/www/html
