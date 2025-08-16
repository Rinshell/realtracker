FROM node:22

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    vim \
    zip \
    unzip \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Изменение текущего пользователя
USER node

# Рабочая директория
WORKDIR /var/www

EXPOSE 5173

# Удерживаем контейнер в активном состоянии
CMD ["tail", "-f", "/dev/null"]