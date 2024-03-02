FROM php:8.1-apache-bullseye

RUN apt-get update && apt-get -qy install --no-install-recommends wget git zip unzip vim ffmpeg

RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer && rm -rf /tmp/composer-setup.php

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN curl -s https://deb.nodesource.com/setup_16.x | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN apt-get -qy install --no-install-recommends nodejs libcurl3-dev libicu-dev mariadb-client

RUN docker-php-ext-install pdo_mysql && docker-php-ext-install intl

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN npm install --global yarn

COPY composer.json .

RUN composer install
RUN php bin/console assets:install --symlink

COPY ./docker/php/000-default.conf /etc/apache2/sites-available/dev-default-ssl.conf
COPY  ./docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -subj "/C=FR/ST=IDF/L=Paris/O=Malerbati/OU=IT Department/CN=vocablist.local" -keyout /etc/ssl/private/vocablist.local.key -out /etc/ssl/certs/vocablist.local.crt

RUN a2enmod rewrite ssl headers proxy proxy_http

RUN a2ensite dev-default-ssl.conf

