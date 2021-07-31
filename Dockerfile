FROM php:8-cli-alpine3.14

COPY . .

WORKDIR .

ENTRYPOINT ./vendor/bin/phpunit ./Tests