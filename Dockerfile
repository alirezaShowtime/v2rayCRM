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


COPY ./dockerConf/default.conf /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html

RUN rm -rf /var/www/html/dockerConf

RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

WORKDIR /var/www/html

RUN composer install

RUN mkdir -p /var/www/html/public

RUN chown -R www-data:www-data /var/www/html

USER $user

RUN php artisan key:generate \
    && php artisan storage:link
