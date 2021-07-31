FROM php:8-cli

# PHP ENV
RUN apt-get update && apt-get install -y curl git libzip-dev zip sudo \
&& docker-php-ext-install zip

# NODE ENV
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
RUN apt-get install -y nodejs

COPY . ./project
WORKDIR ./project

# PHP Dependencies
RUN curl -sS https://getcomposer.org/installer | php
RUN chmod a+x composer.phar
RUN ./composer.phar install

# Node Depenencies
RUN npm install

ENTRYPOINT ./vendor/bin/phpunit ./Tests