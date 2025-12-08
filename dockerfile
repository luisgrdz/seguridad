# 1. Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 2. Instalamos dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql zip bcmath opcache

# -------------------------------------------------------
# 3. INSTALACIÓN DE NODE.JS (Para arreglar el error de Vite)
# -------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 4. Configuración de Apache (Document Root a /public)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf | true
RUN a2enmod rewrite

# 5. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copiamos el código
WORKDIR /var/www/html
COPY . .

# 7. Instalamos dependencias de PHP (Backend)
RUN composer install --no-dev --optimize-autoloader

# -------------------------------------------------------
# 8. COMPILAMOS LOS ASSETS (CSS/JS)
# -------------------------------------------------------
RUN npm install
RUN npm run build

# 9. Permisos (Importante para Laravel)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Exponemos el puerto
EXPOSE 80