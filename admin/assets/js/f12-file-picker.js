jQuery(document).ready(function ($) {
    $(document).on('click', ".f12-file-picker tr", function (e) {
        'use strict';
        e.preventDefault();

        // Read options
        var callback = $(this).parent().parent().attr("data-callback");
        var output = $(this).parent().parent().attr("data-output");
        var output_type = $(this).parent().parent().attr("data-output-type");

        // Get the meta info
        var file, title, id;

        id = $(this).find("td:nth-of-type(1)").text().trim();
        title = $(this).find("td:nth-of-type(2)").text().trim();
        file = $(this).find("td:nth-of-type(3)").text().trim();

        switch(callback){
            case 'f12_metabox_add':
                f12_metabox_add(id, file, title, output, output_type);
                break;
            default:
                f12_file_picker_default(id, file, title);
        }

        // Remove the thickbox
        self.parent.tb_remove();
    });

    function f12_file_picker_default(id, file, title){
        // add the meta info to the output
        $("#f12d_download_id").val(id);
        $("#f12d_download_file").text(file);

    }

    function f12_metabox_add(id, file, title, output, output_type){
        // extend all given fields by the new download
        if(output_type == "li"){
            $("."+output).append("<li>" +
                "<input type=\"hidden\" name=\"f12_download[]\" value=\""+id+"\" />" +
                file+ " (<a href=\"javascript:void(0);\" class=\"f12-download-remove\">Entfernen</a>)" +
                "</li>");
        }else{
            $("."+output).append("<div>" +
                "<input type=\"text\" name=\"f12_download[]\" value=\""+id+"\" />" +
                file+ " (<a href=\"javascript:void(0);\" class=\"f12-download-remove\">Entfernen</a>)" +
                "</div>");
        }
    }
});

/**
 * Callback for the File Picker
 */
jQuery(document).ready(function($){
    var filepicker = $(".f12-file-picker__content");
    var pageinator = filepicker.find(".f12-pageinator");
    var table = filepicker.find(".f12-file-picker tbody");

    pageinator.find(".f12-button").on("click",function(){
        pageinator.find(".f12-button").removeClass("active");
        $(this).addClass("active");

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'f12d_filepicker_get_page',
                page: $(this).attr("data-value")
            },
            success: function(response){
                // changing the table content with the <tr><td> stuff returned
                table.html(response);
            },
            error: function(error){
                console.log("error while trying to get the page");
                console.log(error);
            }
        });
    });
});

/**
 * Callback for the search of  files in the filepicker
 */
jQuery(document).ready(function($){
    var filepicker = $(".f12-file-picker__content");
    var search = filepicker.find("input[name='search']");
    var optionbar = filepicker.find("f12-option-bar");
    var table = filepicker.find(".f12-file-picker tbody");

    $(document).on("keyup",".f12-file-picker__content input[name='search']", function(){

        var value = $(this).val();

        if(value.length >= 3 || value.length === 0){
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'f12d_filepicker_search',
                    value: value
                },
                success: function(response){
                    // changing the table content with the <tr><td> stuff returned
                    table.html(response);
                },
                error: function(error){
                    console.log("error while trying to search for the file");
                    console.log(error);
                }
            });
        }
    });
});