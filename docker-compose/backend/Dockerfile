FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl && \
    pecl install xdebug-3.4.0 && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install pdo_mysql pdo mbstring zip exif pcntl intl bcmath gd && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    # Clear cache
    apt-get clean && rm -rf /var/lib/apt/lists/*

ARG INSTALL_MYSQL_CLIENT=false

RUN if [ ${INSTALL_MYSQL_CLIENT} = true ]; then \
    apt-get update -yqq && \
    apt-get -y install mariadb-client \
;fi

# Add user for laravel application
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

#WORKDIR /var/www
#COPY . ./
#RUN chown -R www:www-data /var/www

WORKDIR /var/www/public
RUN ln -s ../storage/app/public storage

# Change current user to www
# USER www

WORKDIR /var/www
# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
