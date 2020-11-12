#!/bin/bash
GLOBIGNORE=".:.."
workdir=/workdir

# Configure PHP
export USE_ZEND_ALLOC=0
export USE_TRACKED_ALLOC=1

# Install composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
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
