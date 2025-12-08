# Usamos PHP 8.2 con Apache (más fácil para empezar)
FROM php:8.2-apache

# Instalamos dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql zip bcmath opcache

# Habilitamos mod_rewrite de Apache (Vital para las URLs amigables de Laravel)
RUN a2enmod rewrite

# Configuramos la carpeta pública de Apache para que apunte a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf | true

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiamos el código del proyecto
WORKDIR /var/www/html
COPY . .

# Instalamos dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Damos permisos a la carpeta storage (CRÍTICO)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponemos el puerto 80 (Render usa este por defecto internamente)
EXPOSE 80