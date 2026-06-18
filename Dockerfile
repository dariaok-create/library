FROM php:8.0-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN chmod -R 755 /var/www/html
COPY . /var/www/html/
EXPOSE 80
