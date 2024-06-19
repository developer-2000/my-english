## по умолчанию в seeder создает админа
- email: admin@admin.admin
- pass: admin

## Для начала регистраций новых пользователей и просмотра своей почты авторизуйтесь на [mailtrap.io](https://mailtrap.io) и замените на свои данные в файле .env
- MAIL_USERNAME=*******
- MAIL_PASSWORD=*******
- APP_URL=http://свой домен
- в консоле запустить выполнение очередей - php artisan queue:work

## Проект создает бекапы базы данных MySql с пакетом spatie/laravel-backup. Пакет работает с такими базами - MySql, PostgreSQL, SQLite, MongoDB.
- Файл middleware - корень\app\Http\Middleware\BackupDatabase.php

## запуск проекта
- npm install
- composer install
- настроить в .env connect к базе и run - php artisan migrate --seed
- В случае если вы создали домен отличный от english.my - замените 'http://english.my/api/' на свой домен в файле \resources\js\services\http_client.js
- php artisan serve

## составные

- настройка browsersync webpack.mix.js
- [1 Bootstrap 4](https://bootstrap-4.ru/docs/4.0/components/tooltips/).
- [2 bootstrap toggle ](http://www.bootstraptoggle.com/).
- [3 flex simulator ](http://cssworld.ru/flex/).
- [3 font awesome ](https://fontawesome.com/v5/icons/trash-alt?f=classic&s=solid).
- [установка для vue](https://www.npmjs.com/package/vue-bootstrap-toggle).
- [3 Ссылка на админ панель](https://adminlte.io/themes/v3/).
- [4 Тултипы для подсказок](https://kabbouchi.github.io/vue-tippy/4.0/features/default.html).
- [helper для него](https://atomiks.github.io/tippyjs/v6/html-content/).
- [5 modal alert ](https://sweetalert2.github.io/#handling-buttons).
- [6 Таблица vue-good-table ](https://xaksis.github.io/vue-good-table/guide/).
- [7 озвучка текста ](https://xhtml.ru/2021/javascript/javascript-text-to-speech-and-its-many-quirks/).

