FROM webdevops/php:7.4

ENV WEB_DOCUMENT_ROOT=/app
ENV WEB_PHP_SOCKET=127.0.0.1:9000

# Install apps and libs
RUN apt-get update && apt-get -y install \
    apt-utils \
    procps \
    bsdtar \
    libaio1 \
    musl-dev \
    gettext \
    libpcre3-dev \
    gzip \
    git \
    software-properties-common \
&& docker-run-bootstrap \
&& docker-image-cleanup \
&& apt-get clean

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run app
EXPOSE 9000