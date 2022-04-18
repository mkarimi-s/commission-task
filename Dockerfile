FROM alpine:3.13

LABEL Maintainer="Mohamad KarimiSalim <mohamad.karimisalim@gmail.com>"

RUN apk --no-cache add php7 php7-opcache php7-json php7-openssl php7-curl \
    php7-zlib php7-xml php7-phar php7-intl php7-dom php7-xmlreader php7-ctype php7-session php7-iconv \
    php7-mbstring php7-xmlwriter php-simplexml php7-exif php7-tokenizer php7-fileinfo php7-gd php7-bcmath curl

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin --version="2.0.0" --filename=composer  | \
    php -- --install-dir=/usr/bin --filename=composer

COPY . .

RUN composer clear-cache && \
    composer config --global repo.packagist composer https://packagist.org && \
    COMPOSER_MEMORY_LIMIT=-1 composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress \
    --no-scripts --optimize-autoloader --prefer-dist && \
    composer dump-autoload

CMD ["ls", "-la"]
