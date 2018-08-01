FROM singledigital/bay-cli:latest

ADD patches /app/patches
ADD scripts /app/scripts
ADD dpc-sdp /app/dpc-sdp

COPY composer.json composer.lock auth.json /app/

RUN composer install --no-dev --optimize-autoloader --prefer-dist --ansi

COPY . /app
