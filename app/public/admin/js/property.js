/**
 * Обновление блока характеристик при изменении выбранных категорий.
 * Отправляет id выбранных категорий в массиве category, получает готовый HTML
 * для .property_all и восстанавливает значения сохранённых характеристик.
 */
(function ($) {
    'use strict';

    var REFRESH_URL = '/admin/ajax/Refresh_Property_Good.php';

    function selectedCategoryIds() {
        var ids = [];
        $('.category_check:checked').each(function () {
            ids.push($(this).val());
        });
        return ids;
    }

    function propertySnapshot() {
        var snap = {};
        $('#property_all').find('[data-property-id]').each(function () {
            var $el = $(this);
            var id = $el.attr('data-property-id');
            if ($el.is(':checkbox')) {
                if (!$.isArray(snap[id])) {
                    snap[id] = [];
                }
                if ($el.is(':checked')) {
                    snap[id].push($el.val());
                }
            } else {
                snap[id] = $el.val();
            }
        });
        return snap;
    }

    function propertyRestore(snap) {
        $('#property_all').find('[data-property-id]').each(function () {
            var $el = $(this);
            var id = $el.attr('data-property-id');
            if (!snap.hasOwnProperty(id)) {
                return;
            }
            if ($el.is(':checkbox')) {
                $el.prop('checked', $.inArray($el.val(), snap[id]) !== -1);
            } else if ($el.is('select')) {
                $el.val(snap[id]);
            } else {
                $el.val(snap[id]);
            }
        });
    }

    function refreshProperties() {
        var snapshot = propertySnapshot();

        $.ajax({
            url: REFRESH_URL,
            type: 'GET',
            data: { category: selectedCategoryIds() },
            dataType: 'html',
            cache: false
        }).done(function (html) {
            $('#property_all').html(html);
            propertyRestore(snapshot);
        }).fail(function () {
            $('#property_all').html('<p class="property_error">Не удалось обновить характеристики</p>');
        });
    }

    $(function () {
        $(document).on('change', '.category_check', refreshProperties);
    });
})(jQuery);