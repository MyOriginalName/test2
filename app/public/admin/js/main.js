/**
 * Сбор данных по кнопке «Проверить отправку».
 * Собирает chpu_category выбранных категорий и значения характеристик
 * текущего набора, отправляет их в проверочный обработчик и показывает ответ.
 */
(function ($) {
    'use strict';

    var PREVIEW_URL = '/admin/ajax/Preview_Good_Payload.php';

    function collectCategories() {
        var cats = [];
        $('.category_check:checked').each(function () {
            var chpu = $(this).attr('data-chpu');
            if (chpu) {
                cats.push(chpu);
            }
        });
        return cats;
    }

    function collectProperties() {
        var grouped = {};
        $('#property_all').find('[data-property-id]').each(function () {
            var $el = $(this);
            var id = $el.attr('data-property-id');
            if (!grouped[id]) {
                grouped[id] = { multiSelect: false, values: null };
            }
            if ($el.is(':checkbox')) {
                grouped[id].multiSelect = true;
                if (!$el.is(':checked')) {
                    return;
                }
                if (grouped[id].values === null) {
                    grouped[id].values = [];
                }
                grouped[id].values.push($el.val());
            } else {
                grouped[id].values = $el.val();
            }
        });

        var propertyMas = {};
        $.each(grouped, function (id, item) {
            if ($.isArray(item.values)) {
                if (item.values.length > 0) {
                    propertyMas[id] = item.values.join(':::');
                }
            } else if (item.values !== null && item.values !== '') {
                propertyMas[id] = item.values;
            }
        });
        return propertyMas;
    }

    $(function () {
        $('#submit_check').on('click', function () {
            var payload = {
                cats: collectCategories(),
                property_mas: collectProperties()
            };

            $('#payload_result').text('Отправка…');

            $.ajax({
                url: PREVIEW_URL,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                dataType: 'text',
                cache: false
            }).done(function (response) {
                $('#payload_result').text(response);
            }).fail(function () {
                $('#payload_result').text('Не удалось отправить запрос');
            });
        });
    });
})(jQuery);