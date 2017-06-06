FROM php:7.1.5-fpm

RUN set -xe \
    && apt-get update \
    && apt-get install -y libxml2-dev curl libcurl4-gnutls-dev libmcrypt-dev zlib1g-dev \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install dom \
    && docker-php-ext-install curl \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install zip

RUN set -xe \
   && curl -sL https://deb.nodesource.com/setup_8.x | bash - \
   && apt-get install -y nodejs

RUN npm install -g yarn

RUN set -xe \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer --install-dir=/usr/bin/ \
    && php -r "unlink('composer-setup.php');"

RUN set -xe \
    && docker-php-ext-install sockets

RUN pecl install redis-3.1.2 \
    && pecl install xdebug-2.5.4 \
    && docker-php-ext-enable redis xdebug

RUN pecl install apcu
RUN echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini

RUN set -xe \
    && docker-php-ext-install opcache
COPY opcache.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

RUN set -xe \
    && sed -i -e "s/pm.max_children = 5/pm.max_children = 50/g" /usr/local/etc/php-fpm.d/www.conf \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host="`/sbin/ip route|awk '/default/ { print $3 }'` >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN usermod -u 1000 www-data

WORKDIR /sharetoall
