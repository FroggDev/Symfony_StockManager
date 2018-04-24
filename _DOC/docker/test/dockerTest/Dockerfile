FROM php:7.2-fpm

RUN apt-get update

RUN ["apt-get", "install", "-y", "nano"]

RUN ["apt-get", "install", "-y" , "git"]

#RUN ["apt-get", "install", "-y", "php7-cli"]

RUN ["apt-get", "install", "-y", "composer"]

RUN pecl install xdebug

RUN docker-php-ext-install pdo pdo_mysql xdebug

RUN docker-php-ext-enable xdebug

RUN echo 'zend_extension="/usr/local/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so"' > /usr/local/etc/php-fpm.d/xdebug.conf
RUN echo 'xdebug.remote_port=9000' >> /usr/local/etc/php-fpm.d/xdebug.conf
RUN echo 'xdebug.remote_enable=1' >> /usr/local/etc/php-fpm.d/xdebug.conf
RUN echo 'xdebug.remote_connect_back=1' >> /usr/local/etc/php-fpm.d/xdebug.conf

RUN git clone https://github.com/FroggDev/Symfony_StockManager /opt/web/

