FROM php:8.2-fpm

# Устанавливаем системные зависимости и расширения
RUN apt-get update && apt-get install -y \
    libonig-dev libzip-dev unzip git curl \
    && docker-php-ext-install pdo_mysql zip mbstring

# Копируем composer и устанавливаем зависимости
WORKDIR /var/www
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --optimize-autoloader --no-dev

# Копируем весь проект
COPY . /var/www

# Генерируем ключ приложения и кешируем конфигурацию
RUN php artisan key:generate \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan l5-swagger:generate

# Открываем порт (для ориентировки; Railway сам найдёт http-службу)
EXPOSE 8000

# Запуск встроенного сервера Laravel на порту $PORT
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=10000"]
