
# Sample hosting panel

#### Установка панели

1. Склонировать репозиторий
```
git clone 
```
2. Запустить composer
```
composer install
```
3. Настроить конфигурацию БД в файле:
```/home/georgy/htdocs/fastvps/www/panel/app/config/parameters.yml```
4. Создать БД, выполнив команды:
```
php app/console doctrine:database:create
php app/console doctrine:schema:create
```

#### Конфигурирование Nginx
1. Добавить ```/etc/nginx/sites-available/multidomains``` файл с подобной конфигурацией:
```
server {
    server_name ~^(www\.)?(?<domain>.+)$;

    location / {
        root /home/www-data/domains/$domain;
    }
}
```

2. Подключить файл ```ln -s /etc/nginx/sites-available/multidomains /etc/nginx/sites-enabled/multidomains```

3. Выполнить перезагрузку Nginx:
```
sudo service nginx restart
```