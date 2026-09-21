-- Тестовые данные для задания "Характеристики товара"
-- MySQL 5.7, кодировка utf8

CREATE DATABASE IF NOT EXISTS catalog_demo DEFAULT CHARACTER SET utf8;
USE catalog_demo;

-- Кодировка соединения для корректного импорта UTF-8 данных
SET NAMES utf8;

-- Категории товаров
DROP TABLE IF EXISTS category;
CREATE TABLE category (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  chpu_category VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO category (id, name, chpu_category) VALUES
  (1, 'Мебель', 'furniture'),
  (2, 'Декор', 'decor'),
  (3, 'Бытовая техника', 'tech');

-- Справочник характеристик.
-- cat_prop: id категорий через запятую; пустая строка = общая характеристика (показывается всегда).
DROP TABLE IF EXISTS property_s;
CREATE TABLE property_s (
  id_property INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  type ENUM('text','number','select','multi_select') NOT NULL DEFAULT 'text',
  unit VARCHAR(64) NOT NULL DEFAULT '',
  hint VARCHAR(255) NOT NULL DEFAULT '',
  cat_prop VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id_property)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO property_s (id_property, name, type, unit, hint, cat_prop) VALUES
  (1, 'Артикул', 'text', '', 'Внутренний код товара', ''),
  (6, 'Описание', 'text', '', 'Краткое описание товара', ''),
  (7, 'Бренд <i>производитель</i>', 'text', '', '', ''),
  (9, 'Комментарий', 'text', '', '<script>alert("hint xss")</script>', ''),
  (2, 'Материал', 'select', '', 'Основной материал изготовления', '1,2'),
  (3, 'Размер', 'multi_select', '', 'Допустимые размеры', '1, 2'),
  (4, 'Срок гарантии', 'number', 'мес', 'В месяцах, целое число', '1'),
  (5, 'Цвет', 'select', '', 'Цветовая гамма', '2'),
  (8, 'Мощность', 'number', 'Вт', 'Потребляемая мощность', '3');

-- Варианты выбора для характеристик типа select / multi_select
DROP TABLE IF EXISTS property_variant;
CREATE TABLE property_variant (
  id_variant INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_property INT UNSIGNED NOT NULL,
  value VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_variant),
  KEY idx_property (id_property)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO property_variant (id_variant, id_property, value) VALUES
  (1,  2, 'Дерево <b>массив</b>'),
  (2,  2, 'МДФ'),
  (3,  2, 'Металл'),
  (4,  3, 'Малый'),
  (5,  3, 'Средний'),
  (6,  3, 'Большой'),
  (7,  3, 'Очень большой'),
  (8,  5, 'Белый'),
  (9,  5, 'Чёрный'),
  (10, 5, 'Красный'),
  (11, 8, '— для числа варианты не используются —');