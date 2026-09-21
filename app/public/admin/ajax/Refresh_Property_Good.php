<?php
/**
 * AJAX-обработчик: возвращает HTML для блока .property_all
 * по выбранным категориям.
 *
 * Вход: массив category[] с id категорий (GET или POST), либо JSON-тело
 * вида {"category":[1,2]}.
 * Пустой или некорректный category не вызывает ошибок и возвращает
 * только общие характеристики.
 */

require_once dirname(__FILE__) . '/../../inc/db.php';
require_once dirname(__FILE__) . '/../../inc/property_render.php';

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$categoryIds = array();

$rawBody = file_get_contents('php://input');
if (trim($rawBody) !== '') {
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded) && isset($decoded['category']) && is_array($decoded['category'])) {
        $categoryIds = $decoded['category'];
    }
}

if (!is_array($categoryIds) || empty($categoryIds)) {
    $categoryIds = array();
    if (isset($_GET['category'])) {
        $categoryIds = $_GET['category'];
    } elseif (isset($_POST['category'])) {
        $categoryIds = $_POST['category'];
    }
}

if (!is_array($categoryIds)) {
    $categoryIds = array();
}

echo render_properties(db_conn(), $categoryIds);