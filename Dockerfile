FROM php:8.2-fpm
# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip
# Limpiar cache de apt para reducir tamaño de imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# Instalar extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd opcache

# Configurar opcache (acelera mucho la ejecución de PHP, sobretodo con bind mounts en Windows)
# revalidate_freq=0 + validate_timestamps=1 → en dev, detecta cambios al instante
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=0'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'realpath_cache_size=4096K'; \
    echo 'realpath_cache_ttl=600'; \
} > /usr/local/etc/php/conf.d/opcache.ini
# Instalar Composer (gestor de dependencias PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Configurar directorio de trabajo
WORKDIR /var/www
# Exponer puerto PHP-FPM
EXPOSE 9000
# Ejecutar como usuario www-data (seguridad)
USER www-data
# Comando por defecto: arrancar PHP-FPM
CMD ["php-fpm"]