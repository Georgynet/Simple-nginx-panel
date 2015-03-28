
# Simple nginx panel

#### Установка панели

1. Склонировать репозиторий
<pre>
git clone git@github.com:Georgynet/Simple-nginx-panel.git
</pre>

2. Запустить composer
<pre>
composer install
</pre>

3. Настроить конфигурацию БД в файле:
<pre>
/home/georgy/htdocs/fastvps/www/panel/app/config/parameters.yml
</pre>

4. Создать БД, выполнив команды:
<pre>
php app/console doctrine:database:create
php app/console doctrine:schema:create
</pre>

#### Конфигурирование Nginx

1. Добавить `/etc/nginx/sites-available/multidomains` файл с подобной конфигурацией:
    ``` ini
    server {
        server_name ~^(www\.)?(?<domain>.+)$;
    
        location / {
            root /home/www-data/domains/$domain;
        }
    }
    ```

2. Подключить файл `ln -s /etc/nginx/sites-available/multidomains /etc/nginx/sites-enabled/multidomains`

3. Выполнить перезагрузку Nginx:
`
sudo service nginx restart
`

#### Использование

Для создания сайта кликнуть по ссылке "Создать сайт". Для редактирования сайта кликнуть на ссылку "Подробнее", на против интересующего сайта.

Документация по API доступна по ссылке: `{panel_domain}/api/doc/`
