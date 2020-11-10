DIR=`dirname $0`
PHP_DIR=$1
ZIPBALLS=$2
sudo docker build --tag php-crater $DIR
sudo docker run \
    -v $PHP_DIR/sapi/cli/php:/usr/bin/php:ro \
    -v $PHP_DIR/modules/opcache.so:/usr/lib/php/opcache.so:ro \
    -v $ZIPBALLS:/zipballs:ro \
    php-crater
