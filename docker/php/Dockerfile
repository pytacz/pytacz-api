FROM php:7.1-fpm

RUN apt update && apt install -y libpq-dev sqlite3 libsqlite3-dev
RUN docker-php-ext-install pdo_pgsql pdo_sqlite