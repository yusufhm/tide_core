FROM amazeeio/php:7.1-cli-drupal

ENV WEBROOT=docroot \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_CACHE_DIR=/tmp/.composer/cache

RUN apk update \
    && apk del nodejs nodejs-current yarn \
    && apk add nodejs-npm patch rsync --update-cache --repository http://dl-3.alpinelinux.org/alpine/v3.7/main/ \
    && rm -rf /var/cache/apk/*

# @todo: Remove the line below once settings moved to base images.
COPY .bay /bay

ADD patches /app/patches
ADD scripts /app/scripts
ADD dpc-sdp /app/dpc-sdp

COPY composer.json composer.lock /app/

RUN composer install --no-dev --optimize-autoloader --prefer-dist --ansi

COPY . /app