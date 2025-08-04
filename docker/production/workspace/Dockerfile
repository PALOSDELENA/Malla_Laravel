FROM php:8.3-fpm

# Instala extensiones necesarias de PHP para Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el código (se sobrescribirá por el volumen de docker-compose)
COPY . /var/www

# Establece permisos adecuados (si no se manejan desde el host)
RUN chown -R www-data:www-data /var/www

# Establece usuario por defecto
USER www-data
