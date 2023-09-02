FROM php:8.1-fpm

# Copy composer.json
COPY composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libonig-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    libpq-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libreoffice \
    default-jre \
    libreoffice-java-common \
    procps \
    ssh-client \
    libmagickwand-dev --no-install-recommends \
    tzdata

ARG TZ
RUN cp /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick

# Install extensions
RUN docker-php-ext-install mbstring zip exif pcntl pgsql pdo_pgsql
RUN docker-php-ext-enable pgsql pdo_pgsql
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user and user group
ARG USER_GROUP_ID
ARG USER_ID
RUN groupadd -g $USER_GROUP_ID www
RUN useradd -u $USER_ID -ms /bin/bash -g www www

# Copy existing application directory contents
COPY ./ /var/www

# Copy existing application directory permissions
COPY --chown=www:www ./ /var/www

# Change current user to www
USER www
RUN composer install

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]