FROM php:8.0-apache

# Устанавливаем системные зависимости и расширения PHP
RUN apt-get update && apt-get install -y \
        libmariadb-dev \
        libsqlite3-dev \
        && docker-php-ext-install pdo pdo_mysql pdo_sqlite \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/*

# Устанавливаем права на директорию
RUN chmod -R 755 /var/www/html

# Копируем код приложения
COPY . /var/www/html/

# Открываем порт 80
EXPOSE 80
