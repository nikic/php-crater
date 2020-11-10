FROM ubuntu:focal

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y \
        git \
        libaspell15 \
        libasan6 \
        libc-client2007e \
        libcurl4 \
        libedit2 \
        libenchant-2-2 \
        libonig5 \
        libpng16-16 \
        libpq5 \
        libsodium23 \
        libsqlite3-0 \
        libsnmp35 \
        libtidy5deb1 \
        libxml2 \
        libxslt1.1 \
        libzip5 \
        zip

COPY php.ini /usr/local/lib/php.ini
COPY runner.sh .
CMD chmod +x runner.sh && ./runner.sh 2>&1
