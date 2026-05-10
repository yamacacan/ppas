FROM php:8.2-fpm

# Gerekli paketler
RUN apt-get update && apt-get install -y \
    zip unzip git \
    libzip-dev \
    libxml2-dev \
    libldap2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev

# ZIP extension
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# LDAP extension
RUN docker-php-ext-configure ldap --with-ldap=/usr
RUN docker-php-ext-install ldap

# GD extension (MPDF & Spreadsheet için ZORUNLU)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --enable-gd

RUN docker-php-ext-install gd

# XML ve PDO MySQL
RUN docker-php-ext-install xml pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP & PHP-FPM özel ayarları (40 client için optimize)
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-www.conf

WORKDIR /var/www

# Proje dosyaları
COPY . .

# Laravel cache permission
RUN mkdir -p /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/bootstrap/cache

# ENV dosyası
COPY .env .env

# Composer prod install
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Laravel optimization
RUN php artisan key:generate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

CMD ["php-fpm"]
