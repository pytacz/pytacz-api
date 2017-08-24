# pytacz-api

This is API written in Symfony for Pytacz application.

## Requirements
- Composer
- Docker
- PHPUnit

## Installation
- `docker-compose up`
- `composer install`
- `php bin/console doctrine:database:create`
- `php bin/console doctrine:schema:update --force`
- `php bin/console doctrine:database:create --env=test`
- `php bin/console doctrine:schema:update --env=test --force`
- `openssl genrsa -out var/jwt/private.pem -aes256 4096`
    * key should match `jwt_key_pass_phrase` from parameters.yml
- `openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem`
    * key should match `jwt_key_pass_phrase` from parameters.yml 