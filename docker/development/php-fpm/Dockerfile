# Etapa base
FROM php:8.4-fpm as base

RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev libonig-dev libicu-dev \
    && docker-php-ext-install pdo_mysql zip intl

WORKDIR /var/www

# --- Etapa desarrollo ---
FROM base as development

ARG XDEBUG_ENABLED=true

RUN if [ "$XDEBUG_ENABLED" = "true" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug; \
    fi

# --- Etapa producción ---
FROM base as production

USER root

# Configurar límites PHP (incluye tu límite de subida)
RUN echo "max_execution_time=600" > /usr/local/etc/php/conf.d/99-overrides.ini \
    && echo "memory_limit=1024M" >> /usr/local/etc/php/conf.d/99-overrides.ini \
    && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/99-overrides.ini \
    && echo "upload_max_filesize=10M" >> /usr/local/etc/php/conf.d/99-overrides.ini

# Crear usuario www
ARG UID=1000
ARG GID=1000

RUN if getent group ${GID}; then \
      group_name=$(getent group ${GID} | cut -d: -f1); \
      useradd -m -u ${UID} -g ${GID} -s /bin/bash www; \
    else \
      groupadd -g ${GID} www && \
      useradd -m -u ${UID} -g www -s /bin/bash www; \
      group_name=www; \
    fi

# Ajustar PHP-FPM
RUN sed -i "s/user = www-data/user = www/g" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/group = www-data/group = $group_name/g" /usr/local/etc/php-fpm.d/www.conf

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
