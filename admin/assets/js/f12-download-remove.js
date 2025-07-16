jQuery(document).ready(function ($) {
    $(document).on('click', '.f12-download-remove', function (e) {
        'use strict';
        e.preventDefault();

        $(this).parent().remove();
    });
});