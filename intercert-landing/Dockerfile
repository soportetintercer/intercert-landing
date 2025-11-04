# Dockerfile para INTERCERT Landing Page
FROM php:8.1-apache

# Instalar extensiones necesarias de PHP
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install curl \
    && docker-php-ext-install zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite headers

# Configurar Apache para permitir .htaccess y habilitar PHP
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    <FilesMatch \.php$>\n\
        SetHandler application/x-httpd-php\n\
    </FilesMatch>\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Configurar PHP
RUN echo 'display_errors = Off' >> /usr/local/etc/php/php.ini && \
    echo 'log_errors = On' >> /usr/local/etc/php/php.ini && \
    echo 'error_log = /var/log/apache2/php_errors.log' >> /usr/local/etc/php/php.ini && \
    echo 'post_max_size = 20M' >> /usr/local/etc/php/php.ini && \
    echo 'upload_max_filesize = 20M' >> /usr/local/etc/php/php.ini

# Copiar archivos del proyecto
COPY . /var/www/html/

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Exponer puerto 80
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
