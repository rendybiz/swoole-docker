FROM php:8.1.0-cli

ENV DEBIAN_FRONTEND noninteractive
ENV TERM            xterm-color

ARG DEV_MODE
ENV DEV_MODE $DEV_MODE

ENV TINI_VERSION v0.19.0
RUN dpkgArch="$(dpkg --print-architecture)" && curl -s -L -o /tini https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini-${dpkgArch}
RUN chmod +x /tini

COPY ./rootfilesystem/ /

COPY ./app/install-php-extensions /usr/bin/

RUN chmod +x /usr/bin/install-php-extensions

RUN install-php-extensions pcntl

RUN install-php-extensions intl

RUN \
    curl -sfL https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
    chmod +x /usr/bin/composer                                                                     && \
    composer self-update 2.1.6                                                    && \
    apt-get update              && \
    apt-get install -y             \
        libcurl4-openssl-dev       \
        libpq-dev                  \
        libssl-dev                 \
        supervisor                 \
        unzip                      \
        zlib1g-dev                 \
        --no-install-recommends && \
    install-swoole.sh 4.11.1 \
        --enable-http2   \
        --enable-mysqlnd \
        --enable-openssl \
        --enable-sockets --enable-swoole-curl --enable-swoole-json --with-postgres && \
    mkdir -p /var/log/supervisor && \
    rm -rf /var/lib/apt/lists/* $HOME/.composer/*-old.phar /usr/bin/qemu-*-static

# ENTRYPOINT ["/tini", "-g", "--", "/entrypoint.sh"]

CMD [ "/var/www/mezzio/vendor/bin/laminas", "mezzio:swoole:start"]

WORKDIR "/var/www/mezzio"