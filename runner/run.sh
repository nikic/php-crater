SCRIPT=`realpath $0`
DIR=`dirname $SCRIPT`
REPOS=`dirname $DIR`/repos

PHP_DIR=$1
REPO_LIST=$2

sudo docker build --tag php-crater $DIR
sudo docker run \
    -v $PHP_DIR/sapi/cli/php:/usr/bin/php:ro \
    -v $PHP_DIR/modules/opcache.so:/usr/lib/php/opcache.so:ro \
    -v $REPOS:/repos:ro \
    -v $REPO_LIST:/repo_list:ro \
    -v $DIR/cache:/root/.cache:rw \
    php-crater
