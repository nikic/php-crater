#!/bin/bash
GLOBIGNORE=".:.."
workdir=/workdir

# Configure PHP
export USE_ZEND_ALLOC=0
export USE_TRACKED_ALLOC=1

# Install composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'c31c1e292ad7be5f49291169c0ac8f683499edddcfd4e42232982d0fd193004208a58ff6f353fde0012d35fdd72bc394') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

while read -u 10 repo; do
    echo "REPO $repo"
    mkdir -p $workdir
    cp -r $repo/* $workdir
    pushd $workdir

    echo "Installing..."
    timeout 300 ../composer.phar install --no-progress --ignore-platform-req=php

    echo "Testing..."
    timeout 300 vendor/bin/phpunit

    popd
    rm -rf $workdir
done 10</repo_list
