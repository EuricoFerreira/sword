# sword
Sword Challenge

This is my approach to the challenge using Laravel.

## Installation


```bash
git clone --recurse-submodules https://github.com/EuricoFerreira/sword.git

cd sword

cp .env.example .env
//Edit ".env" as follows.

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root

*Create the database to the "127.0.0.1" mysql server.
CREATE DATABASE `laravel` DEFAULT CHARACTER SET utf8;

cd laradock

cp .env.example .env

docker-compose up -d nginx mysql phpmyadmin
docker-compose exec workspace bash

composer install

php artisan migrate

php artisan passport:install

php artisan test

php artisan queue:work
````

## Endpoints
- POST /api/register
- POST /api/login
- GET /api/task 
- POST /api/task
- GET /api/task/{taskId}
- PUT /api/task/{taskId}
- DELETE /api/task/{taskId}



