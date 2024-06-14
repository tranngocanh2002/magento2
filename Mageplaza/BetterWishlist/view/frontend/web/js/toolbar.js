require(['jquery'], function ($) {
    'use strict';

    $(document).ready(function () {
        $('[data-role="direction-switcher"]').on('click', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            if (url) {
                window.location.href = '?list_order=name' + url;
            }
        });

        $('#sorter').on('change', function () {
            var selectedValue = $(this).val();
            var baseUrl = window.location.href.split('?')[0];

            if (selectedValue == 'name'){
                window.location.href = baseUrl + '?list_order=' + selectedValue + '&product_list_dir=asc';
            } else {
                window.location.href = baseUrl;
            }
        });
    });
});
