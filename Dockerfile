FROM php:8.0-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libcurl4-openssl-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && docker-php-ext-install curl

COPY . /app

WORKDIR /app

RUN if [ -f composer.json ]; then composer install; fi

COPY .env /app/.env

CMD ["php", "-S", "0.0.0.0:8080", "bot.php"]
