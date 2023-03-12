FROM php:8.2-fpm-alpine

ENV THUMBNAIL_PATH=/thumbnails

COPY .docker/docker-php-entrypoint /usr/local/bin/docker-php-entrypoint
RUN apk add --no-cache shadow imagemagick ghostscript 

