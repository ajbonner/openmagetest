ARG PHP_BASE_IMAGE=php:8.4-trixie

FROM ${PHP_BASE_IMAGE} AS builder

SHELL ["/bin/bash", "-xeo", "pipefail", "-c"]

ENV MAKEFLAGS="-j$(nproc)"

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  libicu-dev \
  libcurl4-openssl-dev \
  libgd-dev \
  libxml2-dev \
  libzip-dev \
  zlib1g-dev \
  && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype --with-avif --with-xpm \
  && docker-php-ext-install -j$(nproc) ctype gd intl pcntl pdo_mysql simplexml soap zip

FROM ${PHP_BASE_IMAGE} AS runtime

SHELL ["/bin/bash", "-xeo", "pipefail", "-c"]

# Install ONLY runtime dependencies (no -dev or recommended packages)
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  default-mysql-client \
  libicu76 \
  libcurl4 \
  libgd3 \
  libxml2 \
  libzip5 \
  libzstd1 \
  zlib1g \
  && rm -rf /var/lib/apt/lists/*

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/include/php/ /usr/local/include/php/

RUN echo 'memory_limit=256M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS -LN -o /usr/bin/modman https://raw.github.com/colinmollenhour/modman/master/modman && \
    chmod a+x /usr/bin/modman

RUN curl -sS -o n98-magerun.phar https://files.magerun.net/n98-magerun.phar &&  \
    curl -sS -o n98-magerun.phar.sha256 https://files.magerun.net/sha256.php?file=n98-magerun.phar && \
    shasum -a 256 -c n98-magerun.phar.sha256 && \
    chmod +x n98-magerun.phar && \
    mv n98-magerun.phar /usr/bin/magerun.phar
