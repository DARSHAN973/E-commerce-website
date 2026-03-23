# Use an official PHP image with Apache
FROM php:8.2-apache

# Install the PostgreSQL driver (required for Supabase)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy your website files into the container
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
