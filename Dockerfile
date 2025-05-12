FROM php:8.2-cli

# Установка системных зависимостей и расширений PHP
RUN apt-get update && apt-get install -y \
    libzip-dev zip \
    && docker-php-ext-install pdo_mysql zip

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . /app

# Установка зависимостей проекта
RUN composer install --optimize-autoloader --no-dev

# Запуск сервера на переменной $PORT
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t public"]
