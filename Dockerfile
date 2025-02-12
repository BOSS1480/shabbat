# בחר תמונת בסיס שמתאימה ל-PHP
FROM php:8.0-cli

# התקנת התלויות הנדרשות (אם יש)
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl

# הוספת קוד האפליקציה
COPY . /app

# הגדרת ספריית העבודה
WORKDIR /app

# הוספת קובץ ה-.env
COPY .env /app/.env

# הגדרת הפקודה להפעיל את הבוט
CMD ["php", "-S", "0.0.0.0:8080", "bot.php"]
