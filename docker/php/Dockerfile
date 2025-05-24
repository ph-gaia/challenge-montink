FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libc-client-dev \
    libkrb5-dev \
    libonig-dev \
    git \
    curl \
    zip \
    unzip \
    nano \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mysqli \
    mbstring \
    zip \
    exif \
    pcntl \
    gd \
    imap

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create system user to run Composer and Artisan Commands
RUN groupadd -g 1000 www && \
    useradd -u 1000 -g www -s /bin/bash -m www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www/html

# Change current user to www
USER www

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]