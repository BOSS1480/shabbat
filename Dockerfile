# בחר תמונת בסיס של PHP
FROM php:8.0-cli

# התקנת תלות cURL, git ו-Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libcurl4-openssl-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && docker-php-ext-install curl

# הוספת קוד האפליקציה
COPY . /app

# הגדרת ספריית העבודה
WORKDIR /app

# אם יש קובץ composer.json, הפעל את Composer להתקנת התלויות
RUN if [ -f composer.json ]; then composer install; fi

# הוספת קובץ ה-.env
COPY .env /app/.env

# הגדרת הפקודה להפעיל את הבוט
CMD ["php", "-S", "0.0.0.0:8080", "bot.php"]
