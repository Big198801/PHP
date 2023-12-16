FROM php:8.2-fpm

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini
COPY ./my.conf /usr/local/etc/php-fpm.d/my.conf

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
