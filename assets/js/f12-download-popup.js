jQuery(document).ready(function ($) {
    $(document).on('click', '.f12_popup_open', function (e) {
        'use strict';
        e.preventDefault();

        var valid_id = $(this).attr("data-key");

        if(typeof(valid_id) === "undefined"){
            return;
        }

        $("#"+valid_id).css("display","block");
    });
});