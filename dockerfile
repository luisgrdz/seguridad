# 1. Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 2. Instalamos dependencias y extensiones
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql zip bcmath opcache

# 3. Instalamos Node.js (Para los estilos)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# -------------------------------------------------------
# 4. CONFIGURACIÓN DE APACHE (EL ARREGLO DEL ERROR 404) 🚑
# -------------------------------------------------------
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Cambiamos la ruta raiz
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf | true

# Activamos el módulo rewrite
RUN a2enmod rewrite

# ¡ESTA LÍNEA ARREGLA EL ERROR 404! 
# Permite que el archivo .htaccess funcione
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 5. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copiamos el código
WORKDIR /var/www/html
COPY . .

# 7. Instalamos dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# 8. Compilamos estilos
RUN npm install
RUN npm run build

# 9. Permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Exponemos puerto
EXPOSE 80