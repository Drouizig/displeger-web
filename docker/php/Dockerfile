FROM composer

FROM php:7.4-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/local/bin/composer


RUN apk add --no-cache bash
RUN apk add --no-cache ca-certificates icu-libs git unzip zlib-dev libzip libzip-dev
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev
RUN apk add --no-cache freetype libpng libjpeg libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev xvfb coreutils poppler-utils postgresql-dev

RUN apk add --no-cache \
    libgcc libstdc++ libx11 glib libxrender libxext libintl \
    libcrypto1.1 libssl1.1 \
    ttf-dejavu ttf-droid ttf-freefont ttf-liberation ttf-ubuntu-font-family

RUN docker-php-ext-install intl zip exif
RUN docker-php-ext-install -j$(nproc) iconv
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-enable opcache
RUN docker-php-ext-enable exif
RUN docker-php-ext-install pdo_pgsql



RUN echo "short_open_tag = off" >> /usr/local/etc/php/php.ini
RUN echo "memory_limit = 3G" >> /usr/local/etc/php/php.ini
RUN echo "apc.enable_cli = 1" >> /usr/local/etc/php/php.ini
RUN echo "date.timezone = Europe/Paris" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "opcache.max_accelerated_files = 20000" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "realpath_cache_size=4096K" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "realpath_cache_ttl=600" >> /usr/local/etc/php/conf.d/symfony.ini
RUN echo "memory_limit = 3G" >> /usr/local/etc/php/conf.d/symfony.ini
RUN apk del .build-deps
RUN apk add gosu --update --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing/ --allow-untrusted
RUN addgroup bar
RUN adduser -D -h /home -s /bin/sh -G bar foo

ADD entrypoint.sh /entrypoint

RUN chmod a+x /entrypoint

ENTRYPOINT ["/entrypoint"]
