FROM webdevops/php-nginx:8.2

COPY . /app
WORKDIR /app

COPY ./docker_config.php ./conf/config.php
COPY ./docker_php.ini ./php.ini

RUN find . -type d -exec ln -sf "$(pwd)/php.ini" {}/php.ini \;