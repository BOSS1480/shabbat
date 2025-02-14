# השתמש בתמונה של PHP עם Apache
FROM php:8.0-apache

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
