<?php
/**
 * Страница «Новый товар»: форма добавления товара (слева)
 * и тестовые данные MySQL в режиме только для чтения (справа).
 */

require_once dirname(__FILE__) . '/inc/db.php';
require_once dirname(__FILE__) . '/inc/property_render.php';

$pdo        = db_conn();
$categories = $pdo->query('SELECT id, name, chpu_category FROM category ORDER BY id')
                  ->fetchAll(PDO::FETCH_ASSOC);

$dbTables = array(
    'category'         => $pdo->query('SELECT * FROM category ORDER BY id')->fetchAll(PDO::FETCH_ASSOC),
    'property_s'       => $pdo->query('SELECT * FROM property_s ORDER BY id_property')->fetchAll(PDO::FETCH_ASSOC),
    'property_variant' => $pdo->query('SELECT * FROM property_variant ORDER BY id_variant')->fetchAll(PDO::FETCH_ASSOC),
);

include dirname(__FILE__) . '/admin/views/addNewGood.tpl.php';