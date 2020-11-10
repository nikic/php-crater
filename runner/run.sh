SCRIPT=`realpath $0`
DIR=`dirname $SCRIPT`

PHP_DIR=$1
REPOS=$2

sudo docker build --tag php-crater $DIR
sudo docker run \
    -v $PHP_DIR/sapi/cli/php:/usr/bin/php:ro \
    -v $PHP_DIR/modules/opcache.so:/usr/lib/php/opcache.so:ro \
    -v $REPOS:/repos:ro \
    -v $DIR/cache:/root/.cache:rw \
    php-crater
