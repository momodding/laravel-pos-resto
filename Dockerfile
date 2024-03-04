FROM php:8.1-fpm-alpine3.19

ARG user
ARG uid

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

COPY .env .env.example /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    && curl -sL https://deb.nodesource.com/setup_10.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd
RUN pecl install xdebug-2.5.0 && docker-php-ext-enable xdebug

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
