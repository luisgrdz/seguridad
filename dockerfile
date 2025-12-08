# 1. Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 2. Instalamos dependencias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql zip bcmath opcache

# 3. Instalamos Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# -------------------------------------------------------
# 4. CONFIGURACIÓN DE APACHE (MÉTODO INFALIBLE) 💣
# -------------------------------------------------------
# Activamos el módulo rewrite
RUN a2enmod rewrite

# Sobrescribimos el archivo de configuración de sitios de Apache
# Esto fuerza a que apunte a /public y permita el .htaccess
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# -------------------------------------------------------

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

# 9. Permisos (Vitales)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Exponemos puerto
EXPOSE 80