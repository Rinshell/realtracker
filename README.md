# Laravel и Vue (c Vite) на одном домене

## Запуск

Добавить в /etc/hosts запись:

```
127.0.0.1 vue3-laravel-app.local
```

Запустить проект в docker:

```
docker compose up -d
```

Установить зависимости composer:

```
docker compose exec backend composer install
```

Создать .env:

```
cp .env.example .env
```

Сгенерировать ключ приложения:

```
docker compose exec backend php artisan key:generate
```

Выполнить миграции:

```
docker compose exec backend php artisan migrate
```

Открыть в браузере http://vue3-laravel-app.local

Можно менять код на vue. Изменения будут отображаться в браузере

## Сборка frontend для production

Запускаем команду сборки в контейнере node:

```
docker-compose exec node /bin/bash -lc 'npm run build'
```

В папке public/build появятся файлы сборки.

Чтобы на локальный сайт подключилась версия для production отключаем контейнер node (где запущен vite):

```
docker-compose stop node
rm public/hot
```

Важно, чтобы не стало файла `public/hot`!

Если нужно сборку для production хранить в репозитории, то убрать папку /public/build из .gitignore.

Чтобы после этих операций снова работать с vite запускаем контейнер node:

```
docker-compose start node
```



# Установка из Git
## Клонирование с GitHub
> git clone git@github.com:SergePlatanov/RealTracker.git RealTracker

## Переименовываем .env.example в .env
в файле .env меняем:<br>
APP_NAME<br>
APP_DEBUG пока можем оставить true<br>
APP_URL указываем доменное имя https://xxx.xxx.com<br>
DB_xxx - меняем все значения, их берем у хостинг провайдера<br>

Ставим composer локально
инструкция https://getcomposer.org/download/

sudo apt-get update
sudo apt-get install php8.2-intl php-zip php-fpm php-mysql php-gd php-bcmath
sudo apt-get install php8.2-sqlite3
php composer.phar update

sudo service nginx restart

генерируем ключ
php artisan key:generate

## Ставим node.js
sudo apt-get install curl
curl -sL https://deb.nodesource.com/setup_16.x | sudo bash -
sudo apt-get install nodejs

## Ставим tailwnd
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
В файл tailwind.config.js добавляем<br>
>module.exports = {<br>
>  content: [<br>
>>    "./resources/**/*.blade.php",<br>
>>    "./resources/**/*.js",<br>
>>    "./resources/**/*.vue",<br>
>  ],
>  theme: {<br>
>    extend: {},<br>
>  },<br>
>  plugins: [],<br>
>}

## Генерируем ключ
php artisan key:generate

## Установка пакетов и зависимостей
php composer.phar install<br>
npm install

https://laravel.com/docs/11.x/upgrade#sanctum

## Восстанавливаем базу данных
mysql -u fuser -p  factorydb < db.sql
php artisan backup:restore

## Восстанавливаем public
1. Копируем папку image
2. Делаем символьную ссылку на папку \storage\app\public<br>
   ln -s ../storage/app/public public/storage
   
## Перенос в Docker
1. В папке realtracker
docker compose down
2. Удаляем все кроме:
- Папки docker-compose
- файла docker-compose.yml
- файла Dockerfile
- .env
3. Копируем проект в папку
4. Создаем символьную метку
   ln -s ../storage/app/public storage
5. Права
   sudo chmod g+wr -R /opt/RealTracker/storage/app/RealTracker
   sudo chmod g+wr -R /opt/RealTracker/storage/logs
6. Запускаем Docker
docker compose up -d --build

## Выполнение команд внутри докера:
docker compose exec app sh
docker compose exec db sh


