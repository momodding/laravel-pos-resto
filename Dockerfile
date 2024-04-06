FROM composer:latest as vendor
RUN mkdir -p /var/www
WORKDIR /var/www
COPY database/ ./database/
COPY composer.* ./
RUN composer install \
   --ignore-platform-reqs \
   --no-interaction \
   --no-plugins \
   --no-scripts \
   --prefer-dist
COPY . .

RUN composer dump-autoload

# Build asset
FROM node:18-alpine as asset
RUN mkdir -p /var/www/public
WORKDIR /var/www
COPY package*.json ./
RUN npm install
COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

FROM php:8.1-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN useradd -G www-data,root -u $uid -d /home/$user $user

WORKDIR /var/www
COPY --from=vendor /usr/bin/composer /usr/bin/composer
COPY --chown=$user:$user . .
COPY --chown=$user:$user --from=vendor /var/www/vendor/ ./vendor/
COPY --chown=$user:$user --from=asset /var/www/public/build/ ./public/build/

RUN php artisan config:cache && \
    php artisan route:cache && \
    chmod 777 -R /var/www/storage/ && \
    chown -R $user:$user /var/www/
USER $user


EXPOSE 9000
CMD ["php-fpm"]