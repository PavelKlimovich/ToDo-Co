FROM php:8.1-fpm
RUN apt-get update
RUN apt-get install -y autoconf pkg-config libssl-dev libzip-dev git gcc make libc-dev vim unzip
RUN docker-php-ext-install bcmath pdo pdo_mysql mysqli sockets zip
RUN pecl install xdebug-3.1.3 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

WORKDIR /home/www

ENTRYPOINT ["php", "-S", "0.0.0.0:8090", "-t", "/home/www/public"]