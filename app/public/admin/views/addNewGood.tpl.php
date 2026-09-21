<?php
/**
 * Шаблон страницы «Новый товар».
 * Доступные переменные: $categories, $dbTables, $pdo.
 */
if (!isset($categories)) {
    $categories = array();
}
if (!isset($dbTables)) {
    $dbTables = array();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <title>Новый товар — характеристики</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font: 14px/1.5 Arial, sans-serif; background: #f4f6f8; color: #222; }
        .header { background: #2b3a4a; color: #fff; padding: 14px 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .layout { display: flex; gap: 24px; padding: 24px; align-items: flex-start; }
        .form-col { flex: 0 1 560px; min-width: 480px; }
        .db-col  { flex: 1 1 auto; overflow-x: auto; }
        .card { background: #fff; border: 1px solid #dde3e8; border-radius: 6px; padding: 20px; margin-bottom: 20px; }
        .card h2 { margin: 0 0 14px; font-size: 15px; color: #2b3a4a; border-bottom: 1px solid #eef1f4; padding-bottom: 8px; }
        .category_check-label { display: block; padding: 6px 0; cursor: pointer; }
        .property_item { padding: 10px 0; border-bottom: 1px dashed #e8edf1; }
        .property_item:last-child { border-bottom: none; }
        .property_name { display: block; font-weight: bold; margin-bottom: 4px; }
        .property_hint { display: block; color: #8aa; margin-top: 3px; font-size: 12px; }
        .property_unit { margin-left: 8px; color: #8aa; }
        .property_value[type="text"], .property_value[type="number"] { width: 320px; max-width: 100%; }
        select.property_value { width: 320px; max-width: 100%; }
        .property_multi .property_checkbox_label { display: inline-block; margin-right: 14px; }
        .btn { background: #2b7de9; color: #fff; border: none; border-radius: 4px; padding: 10px 22px; font-size: 14px; cursor: pointer; }
        .btn:hover { background: #1f66c4; }
        #payload_result { background: #0f1821; color: #9fe6a0; border-radius: 6px; padding: 14px; font-family: Consolas, Monaco, monospace; font-size: 13px; white-space: pre-wrap; word-break: break-all; margin-top: 14px; }
        .db-table { border-collapse: collapse; width: 100%; margin-bottom: 18px; }
        .db-table th, .db-table td { border: 1px solid #ccd5dc; padding: 6px 10px; text-align: left; font-size: 13px; }
        .db-table th { background: #eef2f5; }
        .db-table tr:nth-child(even) td { background: #fafcfd; }
        .db-col h3 { color: #2b3a4a; margin: 10px 0 6px; }
        .read-only-note { color: #7a8a96; font-size: 12px; }
    </style>
</head>
<body>
<div class="header">
    <h1>Новый товар</h1>
</div>

<div class="layout">

    <div class="form-col">
        <form id="good_form" class="card">
            <h2>Категории</h2>
            <div id="category_block">
                <?php foreach ($categories as $category): ?>
                    <label class="category_check-label">
                        <input type="checkbox"
                               class="category_check"
                               name="category[]"
                               value="<?php echo (int)$category['id']; ?>"
                               data-chpu="<?php echo esc($category['chpu_category']); ?>" />
                        <?php echo esc($category['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </form>

        <form id="property_form" class="card">
            <h2>Характеристики</h2>
            <div class="property_all" id="property_all">
                <?php echo render_properties($pdo, array()); ?>
            </div>
        </form>

        <div class="card">
            <h2>Проверка отправки</h2>
            <button type="button" id="submit_check" class="btn">Проверить отправку</button>
            <div id="payload_result"></div>
        </div>
    </div>

    <div class="db-col">
        <div class="card">
            <h2>Данные MySQL <span class="read-only-note">(режим только для чтения)</span></h2>

            <h3>category</h3>
            <table class="db-table">
                <tr><th>id</th><th>name</th><th>chpu_category</th></tr>
                <?php foreach ($dbTables['category'] as $row): ?>
                    <tr>
                        <td><?php echo esc($row['id']); ?></td>
                        <td><?php echo esc($row['name']); ?></td>
                        <td><?php echo esc($row['chpu_category']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>property_s</h3>
            <table class="db-table">
                <tr><th>id_property</th><th>name</th><th>type</th><th>unit</th><th>hint</th><th>cat_prop</th></tr>
                <?php foreach ($dbTables['property_s'] as $row): ?>
                    <tr>
                        <td><?php echo esc($row['id_property']); ?></td>
                        <td><?php echo esc($row['name']); ?></td>
                        <td><?php echo esc($row['type']); ?></td>
                        <td><?php echo esc($row['unit']); ?></td>
                        <td><?php echo esc($row['hint']); ?></td>
                        <td><?php echo esc($row['cat_prop']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>property_variant</h3>
            <table class="db-table">
                <tr><th>id_variant</th><th>id_property</th><th>value</th></tr>
                <?php foreach ($dbTables['property_variant'] as $row): ?>
                    <tr>
                        <td><?php echo esc($row['id_variant']); ?></td>
                        <td><?php echo esc($row['id_property']); ?></td>
                        <td><?php echo esc($row['value']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</div>

<script src="/vendor/jquery/jquery-1.12.4.min.js"></script>
<script src="/admin/js/property.js"></script>
<script src="/admin/js/main.js"></script>
</body>
</html>