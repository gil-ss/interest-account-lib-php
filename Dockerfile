FROM php:8.2-cli

# Install necessary dependencies
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    jq \
    && rm -rf /var/lib/apt/lists/*

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Set working directory
WORKDIR /src

# Copy only composer files first for caching
COPY composer.json /src/

RUN [ -f composer.lock ] && COPY composer.lock /src/ || true

# Install Composer itself
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# Install Composer dependencies
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction

# Copy the rest of the project files
COPY . /src/

# Configure Xdebug for coverage
RUN echo "xdebug.mode=coverage" > /usr/local/etc/php/conf.d/xdebug.ini
