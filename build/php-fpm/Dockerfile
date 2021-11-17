FROM php:7.4-fpm

COPY ./php.ini /usr/local/etc/php/

# Install basic libs
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    vim \
    wget

RUN apt-get update && apt-get install -y \
	zip unzip 

RUN usermod -u 1000 www-data

# Install mysql driver
RUN docker-php-ext-install pdo_mysql

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install symfony CLI
RUN  wget https://get.symfony.com/cli/installer -O - | bash

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
