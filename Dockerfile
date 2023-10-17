FROM php:8.1-apache

RUN a2enmod rewrite
ARG user
ARG uid=1000

RUN apt-get update && apt-get install -y \
        build-essential \
        libpng-dev \
        libonig-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        locales \
        zip \
        jpegoptim optipng pngquant gifsicle \
        vim \
        unzip \
        git \
        libzip-dev\
        curl

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install zip
RUN docker-php-ext-install bcmath
RUN docker-php-source delete

COPY ./ /var/www/html
COPY ./dockerConf/default.conf /etc/apache2/sites-available/000-default.conf
# Create system user to run Composer and Artisan Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

# Set working directory
RUN mkdir -p /var/www/html/public

# COPY ./ /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
USER $user
