FROM php:8.2-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# --- NUEVAS LÍNEAS PARA REPARAR EL ERROR 500 ---
# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite

# Evitar el aviso de ServerName en los logs (opcional pero recomendado)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
# -----------------------------------------------

# 2. Copiar composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 3. Copiar archivos
COPY . .

# 4. Ajustar permisos (Crucial para que Apache pueda leer/escribir archivos)
RUN chown -R www-data:www-data /var/www/html

# Verificar extensiones (debug útil)
RUN php -m

# 5. Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Cambiar el DocumentRoot de Apache a la carpeta /public (necesario para Laravel/etc)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Asegurar que el usuario de Apache sea dueño de los archivos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
    
EXPOSE 80
CMD ["apache2-foreground"]