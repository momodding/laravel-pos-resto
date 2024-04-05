# FROM php:8.1-fpm-alpine3.19
FROM php:8.1-fpm

ARG user
ARG uid

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

COPY .env .env.example /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# ADD ./ZscalerRootCertificate.crt /usr/local/share/ca-certificates/
# RUN apk add --no-cache \
#     --repository http://dl-cdn.alpinelinux.org/alpine/v3.19/main \
#     ca-certificates \
#     openssl \
#     curl
# COPY ca/* /usr/local/share/ca-certificates/
RUN update-ca-certificates

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
# RUN docker-php-ext-install mysqli pdo pdo_mysql
# RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
# RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN useradd -G www-data,root -u $uid -d /home/$user $user

# Add user for npm
# RUN chown -R $user:$(id -gn $user) /home/$user/.config

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=$user:$user . /var/www

# Change current user to www
USER $user

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
