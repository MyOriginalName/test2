<?php
/**
 * Smoke-тесты: доступность тестовой базы и HTML-ответ AJAX-обработчика.
 *
 * Запуск с переменными окружения подключения к БД:
 *   DB_HOST=127.0.0.1 DB_NAME=catalog_demo DB_USER=catalog_user DB_PASSWORD=catalog_password php tests/run.php
 *
 * Возвращает 0 при успешном прохождении всех проверок, 1 при ошибке.
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$root = dirname(__FILE__) . '/..';

ob_start();

$failures = 0;

function check($ok, $label)
{
    global $failures;
    echo ($ok ? '  OK   ' : '  FAIL ') . $label . PHP_EOL;
    if (!$ok) {
        $failures++;
    }
}

echo 'Проверка 1: доступность тестовой базы...' . PHP_EOL;

$pdoAvailable = extension_loaded('pdo_mysql');
check($pdoAvailable, 'расширение pdo_mysql загружено');

if ($pdoAvailable) {
    try {
        require_once $root . '/app/public/inc/db.php';
        $pdo = db_conn();
        $stmt = $pdo->query('SELECT COUNT(*) AS c FROM category');
        $categoryCount = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
        check($categoryCount > 0, 'подключение к БД выполнено, категорий в базе: ' . $categoryCount);
    } catch (Exception $e) {
        echo '   Исключение: ' . $e->getMessage() . PHP_EOL;
        check(false, 'подключение к БД выполнено');
    }
} else {
    check(false, 'подключение к БД выполнено');
}

echo 'Проверка 2: HTML-ответ AJAX-обработчика characteristics...' . PHP_EOL;

$ajaxFile = $root . '/app/public/admin/ajax/Refresh_Property_Good.php';
check(file_exists($ajaxFile), 'файл Refresh_Property_Good.php существует');

if (file_exists($ajaxFile)) {
    require_once $root . '/app/public/inc/db.php';
    require_once $root . '/app/public/inc/property_render.php';

    ob_start();
    echo render_properties(db_conn(), array());
    $htmlNoCats = ob_get_clean();

    check(strpos($htmlNoCats, 'Артикул') !== false, 'без выбранных категорий есть общая характеристика «Артикул»');

    ob_start();
    $_GET['category'] = array('1');
    include $ajaxFile;
    $htmlFurniture = ob_get_clean();

    check(strpos($htmlFurniture, 'Материал') !== false, 'для категории «Мебель» есть характеристика «Материал»');
    check(strpos($htmlFurniture, 'Мощность') === false, 'для категории «Мебель» нет характеристики «Мощность» (только «Техника»)');

    ob_start();
    $_GET['category'] = array('1', '2');
    include $ajaxFile;
    $htmlFurnitureDecor = ob_get_clean();

    check(strpos($htmlFurnitureDecor, 'Цвет') !== false, 'для «Мебель» + «Декор» есть характеристика «Цвет»');

    ob_start();
    $_GET['category'] = array('abc', '-5', '0', '1foo');
    include $ajaxFile;
    $htmlInvalid = ob_get_clean();

    check(strpos($htmlInvalid, 'Warning') === false && strpos($htmlInvalid, 'Fatal error') === false,
        'некорректный category не вызывает warning/fatal error');
    check(strpos($htmlInvalid, 'Артикул') !== false, 'некорректный category возвращает общие поля');
    check(strpos($htmlInvalid, 'Материал') === false,
        'некорректный category не выводит характеристику категории «Материал»');
}

echo PHP_EOL;
if ($failures === 0) {
    echo 'Все проверки пройдены.' . PHP_EOL;
    exit(0);
} else {
    echo 'Провалено проверок: ' . $failures . PHP_EOL;
    exit(1);
}