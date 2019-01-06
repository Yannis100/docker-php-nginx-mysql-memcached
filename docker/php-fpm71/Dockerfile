FROM php:7.1-fpm

COPY php.ini /usr/local/etc/php/conf.d/php.ini

COPY index.php /var/www/html
COPY adminer-4.7.0.php /var/www/html

RUN apt-get update \
  && docker-php-ext-install pdo_mysql mysqli mbstring

RUN apt-get update \
  && apt-get install -y --no-install-recommends libmemcached-dev zlib1g-dev \
  && pecl install igbinary-2.0.8 \
  && docker-php-ext-enable igbinary \
  && pecl install memcached-3.1.3 \
  && docker-php-ext-enable memcached opcache \
  && apt-get autoremove -y \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# The memcached provided via PECL does not support igbinary, should be compiled with --enable-memcached-igbinary