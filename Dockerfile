FROM webdevops/php-nginx-dev:7.3
MAINTAINER Julio Fernandez <jfernandez74@gmail.com>

# Prevent dpkg errors
ENV TERM=xterm-256color

# Update apt
RUN apt-get update

# Install dev dependencies
RUN apt-get install -y \
        apt-utils \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libicu-dev \
        libc-dev \
        libxml2-dev \
        libmcrypt-dev \
        libyaml-dev \
        zlib1g-dev \
        libzip-dev \
        libmagickwand-dev --no-install-recommends

# Install production dependencies
RUN apt-get install -y \
        bash \
        curl \
        wget \
        gnupg \
        git \
        nano \
        zip \
        unzip \
        g++

RUN docker-php-ext-configure intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install soap \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install opcache \
    && docker-php-ext-install mysqli \
    && pecl install -f imagick  \
    && docker-php-ext-enable imagick \
    && pecl install -f apcu \
    && docker-php-ext-enable apcu \
    && pecl install -f mongodb \
    && docker-php-ext-enable mongodb \
    && pecl install -f redis \
    && docker-php-ext-enable redis \
	&& pecl install -f yaml \
    && docker-php-ext-enable yaml

WORKDIR /var/www/Jargon-API/

