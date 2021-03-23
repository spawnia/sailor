FROM php:7.4-cli

WORKDIR /workdir

RUN apt-get update -y \
    && apt-get install -y \
        make \
        git \
        libzip-dev \
        zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install \
        zip \
        mysqli \
        pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
ENV PATH=$PATH:~/.composer/vendor/bin

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
