# See here for image contents: https://github.com/microsoft/vscode-dev-containers/tree/v0.245.2/containers/php/.devcontainer/base.Dockerfile

FROM mcr.microsoft.com/devcontainers/php:0-8.1

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libxml2-dev \
    libpng-dev

# Install php extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    xml \
    zip \
    bcmath \
    pcntl \
    sockets \
    gd

# disable xdebug
RUN rm -f /usr/local/etc/php/conf.d/xdebug.ini

# set memory limit
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;