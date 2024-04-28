# Use an official PHP image
FROM php:latest

# Install zip extension and unzip command
RUN apt-get update \
    && apt-get install -y zip unzip \
    && rm -rf /var/lib/apt/lists/*

# Install Git
RUN apt-get update \
    && apt-get install -y git \
    && rm -rf /var/lib/apt/lists/*

# Set the working directory in the container
WORKDIR /app

# Copy the composer files and install dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code
COPY . .

# Install BC Math extension
RUN docker-php-ext-install bcmath

# Or, install GMP extension
RUN apt-get update && apt-get install -y libgmp-dev && docker-php-ext-install gmp

RUN pecl install xdebug && docker-php-ext-enable xdebug


# Run tests or your application
CMD ["php", "./vendor/bin/phpunit", "tests"]
