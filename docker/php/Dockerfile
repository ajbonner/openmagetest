FROM php:8.4-fpm

RUN apt-get update \
    && apt-get install -y libicu-dev libcurl4-openssl-dev libgd-dev libxml2-dev libzip-dev p7zip zip zlib1g-dev \
    && docker-php-ext-install pdo_mysql opcache ctype dom gd intl simplexml soap zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS -o n98-magerun.phar https://files.magerun.net/n98-magerun.phar &&  \
    curl -sS -o n98-magerun.phar.sha256 https://files.magerun.net/sha256.php?file=n98-magerun.phar && \
    shasum -a 256 -c n98-magerun.phar.sha256 && \
    chmod +x n98-magerun.phar && \
    mv n98-magerun.phar /usr/bin/magerun.phar


