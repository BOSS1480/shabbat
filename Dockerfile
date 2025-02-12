FROM php:8.0-cli

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl

COPY . /app

WORKDIR /app

COPY .env /app/.env

CMD ["php", "bot.php"]
