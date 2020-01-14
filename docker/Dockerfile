FROM php:7.2-apache-stretch

RUN mkdir -p /var/www/html
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/start.sh /usr/local/bin/start
COPY docker/config/php/php.ini /usr/local/etc/php/php.ini

# Install any custom libs
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git \
    libzip-dev \
    zip \
    curl \
    nano \
    unzip \
    libicu-dev \
    libbz2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxpm-dev \
    libvpx-dev \
    libmcrypt-dev \
    libreadline-dev \
    libxml2-dev \
    iproute2 \
    g++ \
    wget

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

# Install various PHP extensions
RUN docker-php-ext-install pdo_mysql \
    -j$(nproc) iconv \
    bz2 \
    soap \
    intl \
    iconv \
    bcmath \
    calendar \
    mbstring \
    zip \
    mysqli \
    -j$(nproc) gd \
    && docker-php-ext-enable mysqli \
    gd \
    && rm -r /var/lib/apt/lists/*

RUN a2enmod rewrite negotiation \
&& chmod +x /usr/local/bin/start

WORKDIR /var/www/html

CMD ["/usr/local/bin/start"]
