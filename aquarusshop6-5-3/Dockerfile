# Base image with PHP 8.2 and Apache
FROM php:8.2-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
 
COPY . /var/www/html/
WORKDIR /var/www/html/

COPY ./docker_config.php ./conf/config.php
COPY ./docker_php.ini ./php.ini
COPY ./docker.htaccess ./.htaccess
COPY ./docker_php.ini /opt/docker/etc/php/php.ini

COPY docker_php.ini /usr/local/etc/php/php.ini

#RUN echo "cgi.force_redirect=0" >> /usr/local/etc/php/php.ini
# Set correct permissions for cache write
RUN chown -R 1000:1000 /var/www/html/* && chmod -R 777 /var/www/html/*

RUN find . -type d -exec ln -sf "$(pwd)/php.ini" {}/php.ini \;