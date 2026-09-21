# Характеристики товара (тестовое задание)

Страница «Новый товар»: форма с категориями и характеристиками (слева) и тестовые
данные MySQL в режиме только для чтения (справа). Выбор категорий изменяет набор
характеристик через AJAX; введённые значения сохраняются; кнопка «Проверить
отправку» показывает собранный payload без записи в базу.

## Стек

- PHP 5.6, MySQL 5.7, PDO (`pdo_mysql`), Apache
- jQuery 1.12 (локально, без CDN), ванильный JS

## Структура

```
app/public/               корень сайта (DocumentRoot)
  admin/views/addNewGood.tpl.php     страница и первичный вывод характеристик
  admin/js/property.js               AJAX-обновление по категориям + сохранение значений
  admin/js/main.js                   сбор данных по кнопке «Проверить отправку»
  admin/ajax/Refresh_Property_Good.php   HTML для .property_all по выбранным категориям
  admin/ajax/Preview_Good_Payload.php    приём и показ payload
  inc/db.php                          подключение к БД (DB_HOST/DB_NAME/DB_USER/DB_PASSWORD)
  inc/property_render.php             отбор и рендер характеристик
  vendor/jquery/                      jQuery 1.12.4
database/init.sql          схема и тестовые данные
tests/run.php              smoke-проверки
SOLUTION.md                сопроводительная документация
```

## Запуск через Docker

```
docker compose up --build -d
```

Откройте http://localhost:8080. Если порт 8080 занят, его можно изменить:

```
APP_PORT=8081 docker compose up --build -d
```

## Запуск локально

Создайте пустую базу MySQL и импортируйте `database/init.sql`, затем из корня репозитория:

```
DB_HOST=127.0.0.1 DB_NAME=catalog_demo DB_USER=catalog_user DB_PASSWORD=catalog_password \
php -S 127.0.0.1:8080 -t app/public
```

Документ-рут должен указывать на `app/public`.

## Проверки

```
docker compose exec -T web php /var/www/tests/run.php
```

либо локально (теми же переменными окружения):

```
DB_HOST=127.0.0.1 DB_NAME=catalog_demo DB_USER=catalog_user DB_PASSWORD=catalog_password \
php tests/run.php
```

Проверяется доступность базы и HTML-ответ AJAX-обработчика, плюс отбор
характеристик по категориям и обработка некорректного `category`.

## Условия задания

- Без выбранных категорий показываются только общие характеристики (`property_s.cat_prop` пуст).
- Названия, подсказки и варианты из БД выводятся как текст (без HTML/JS).
- Значения характеристик сохраняются при AJAX-обновлении.
- `cats` — chpu_category выбранных категорий; `property_mas` — id характеристики → строка,
  мультивыбор через `:::`, числовое `0` отправляется, пустые/отсутствующие в наборе — нет.