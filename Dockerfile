FROM php:8.2-cli

# PHP ENV
RUN apt-get update && apt-get install -y curl git libzip-dev zip sudo keychain ca-certificates gnupg \
&& docker-php-ext-install zip \


# NODE ENV
RUN set -uex; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
     | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
    NODE_MAJOR=20; \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" \
     > /etc/apt/sources.list.d/nodesource.list; \
    apt-get -qy update; \
    apt-get -qy install nodejs;


COPY . ./project
WORKDIR ./project

# PHP Dependencies
RUN curl -sS https://getcomposer.org/installer | php
RUN chmod a+x composer.phar
RUN ./composer.phar install

# Node Depenencies
RUN npm install

ENTRYPOINT ./vendor/bin/phpunit ./Tests && npm run test