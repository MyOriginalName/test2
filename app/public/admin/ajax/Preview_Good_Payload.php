<?php
/**
 * Проверочный обработчик кнопки «Проверить отправку».
 * Ничего не записывает в БД — только принимает параметры и показывает их.
 * Менять не требуется.
 */

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$raw = file_get_contents('php://input');

$data = json_decode($raw, true);
if (!is_array($data)) {
    if (is_array($_POST) && count($_POST) > 0) {
        $data = $_POST;
    } else {
        $data = array('raw' => $raw);
    }
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);