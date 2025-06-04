# Gunakan image PHP dengan Apache
FROM php:8.0-apache

# Install ekstensi PHP yang diperlukan
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy semua file project ke container
COPY . .

# Set hak akses untuk storage dan cache folder
RUN chown -R www-data:www-data writable/ && chmod -R 775 writable/

# Expose port 80
EXPOSE 80

# Jalankan perintah Apache
CMD ["apache2-foreground"]