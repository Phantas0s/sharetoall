#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

# Install mysql driver
# Here you can install any other extension that you need
docker-php-ext-install pdo_mysql

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt-get install git -yqq
apt-get install zip unzip -yqq
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php -yqq
php -r "unlink('composer-setup.php');"
mv composer.phar composer
composer install

# Install phpunit, the tool that we will use for testing
curl --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar
chmod +x /usr/local/bin/phpunit
