FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    libzip-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    unzip \
    git \
    curl \
    mariadb-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN pecl install xdebug-3.3.2 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install gd pdo_mysql mbstring zip exif pcntl intl bcmath

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка локалей
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && locale-gen

# Установка mariadb-client, если указан аргумент (оставляем для совместимости)
ARG INSTALL_MYSQL_CLIENT=false
RUN if [ "${INSTALL_MYSQL_CLIENT}" = "true" ]; then \
    apt-get update \
    && apt-get install -y mariadb-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/*; \
fi

# Добавление пользователя для Laravel
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

WORKDIR /var/www/public
RUN ln -s ../storage/app/public storage

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]