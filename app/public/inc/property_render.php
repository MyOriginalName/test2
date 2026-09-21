<?php
/**
 * Рендер характеристик товара.
 * Единая точка формирования HTML блока .property_all:
 * используется и при первой загрузке страницы, и в AJAX-обработчике.
 */

if (!function_exists('esc')) {
    /**
     * Экранирование значения для вывода как текст (защита от HTML/JS из БД).
     */
    function esc($text)
    {
        return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Варианты выбора для характеристики select / multi_select.
 */
function property_variants($pdo, $propertyId)
{
    $stmt = $pdo->prepare(
        'SELECT id_variant, value FROM property_variant WHERE id_property = ? ORDER BY id_variant'
    );
    $stmt->execute(array((int)$propertyId));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * HTML одной характеристики.
 */
function render_property_item($pdo, $row)
{
    $id   = (int)$row['id_property'];
    $name = esc($row['name']);
    $hint = esc($row['hint']);
    $type = $row['type'];
    $unit = esc($row['unit']);

    $html  = '<div class="property_item">';
    $html .= '<label for="prop_' . $id . '" class="property_name">' . $name . '</label>';

    if ($type === 'number') {
        $html .= '<input type="number" step="any" id="prop_' . $id
               . '" name="property[' . $id . ']" data-property-id="' . $id
               . '" class="property_value" />';
        if ($unit !== '') {
            $html .= '<span class="property_unit">' . $unit . '</span>';
        }
    } elseif ($type === 'select') {
        $html .= '<select id="prop_' . $id . '" name="property[' . $id
               . ']" data-property-id="' . $id . '" class="property_value">';
        $html .= '<option value="">&#8212; выберите &#8212;</option>';
        foreach (property_variants($pdo, $id) as $variants) {
            $html .= '<option value="' . esc($variants['id_variant']) . '">'
                   . esc($variants['value']) . '</option>';
        }
        $html .= '</select>';
    } elseif ($type === 'multi_select') {
        $html .= '<div class="property_multi">';
        foreach (property_variants($pdo, $id) as $variants) {
            $html .= '<label class="property_checkbox_label">'
                   . '<input type="checkbox" class="property_checkbox" data-property-id="' . $id
                   . '" name="property[' . $id . '][]" value="' . esc($variants['id_variant']) . '" /> '
                   . esc($variants['value'])
                   . '</label>';
        }
        $html .= '</div>';
    } else {
        $html .= '<input type="text" id="prop_' . $id . '" name="property[' . $id
               . ']" data-property-id="' . $id . '" class="property_value" />';
    }

    if ($hint !== '') {
        $html .= '<small class="property_hint">' . $hint . '</small>';
    }

    $html .= '</div>';
    return $html;
}

/**
 * Отбор и рендер характеристик по выбранным категориям.
 *
 * Общие характеристики (cat_prop пуст) выводятся всегда.
 * Характеристика категории выводится, если выбран хотя бы один id из её cat_prop.
 *
 * @param PDO   $pdo
 * @param array $categoryIds      id выбранных категорий (может содержать мусор)
 * @return string                 HTML для блока .property_all
 */
function render_properties($pdo, array $categoryIds)
{
    $categoryIds = array_filter($categoryIds, function ($id) {
        return is_numeric($id) && (int)$id > 0;
    });
    $categoryIds = array_map('intval', $categoryIds);
    $categoryIds = array_values(array_unique($categoryIds));

    $rows = $pdo->query('SELECT * FROM property_s ORDER BY id_property')
                ->fetchAll(PDO::FETCH_ASSOC);

    $html = '';

    foreach ($rows as $row) {
        $propertyCategoryIds = array();
        $catProp = trim($row['cat_prop']);

        if ($catProp !== '') {
            foreach (explode(',', $catProp) as $part) {
                $part = trim($part);
                if ($part !== '' && is_numeric($part) && (int)$part > 0) {
                    $propertyCategoryIds[(int)$part] = (int)$part;
                }
            }
        }

        $show = empty($propertyCategoryIds);
        if (!$show) {
            $show = count(array_intersect($propertyCategoryIds, $categoryIds)) > 0;
        }

        if ($show) {
            $html .= render_property_item($pdo, $row);
        }
    }

    return $html;
}