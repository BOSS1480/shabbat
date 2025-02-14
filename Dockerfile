# השתמש בתמונה של PHP עם Apache בגרסה 8.2
FROM php:8.2-apache

# התקנת הרחבות נדרשות
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

# התקנת Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# העתקת הקבצים שלך לתוך התמונה
COPY . /var/www/html/

# התקנת התלותות של Composer
RUN composer install --no-dev --optimize-autoloader

# חשיפת הפורט
EXPOSE 80

# הגדרת הפקודה להפעלת Apache
CMD ["apache2-foreground"]
