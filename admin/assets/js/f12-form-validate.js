function F12FormValidate(jQueryObject) {
    this.m_jQueryObject = null;

    /**
     * Constructor
     * @param jQueryObject
     */
    this.init = function (jQueryObject) {
        this.m_jQueryObject = jQueryObject;

        this.add_action_on_submit();
    };

    /**
     * Adding event on click
     */
    this.add_action_on_submit = function () {
        var c = this;
        this.m_jQueryObject.submit(function (e) {
            c.trigger_on_submit(e);
        });
    };

    /**
     * Test to add required to a wp_editor TINYMCE
     * @param jqDom
     */
    this.is_textarea = function (jQDOM) {
        var nodename = jQDOM[0].nodeName.toLowerCase();
        if (nodename === "textarea") {
            return true;
        }
        return false;
    };

    /**
     * Triggered on click
     * @param e
     */
    this.trigger_on_submit = function (e) {
        var c = this;
        // Find all fields with the .f12-form-validate class
        this.m_jQueryObject.find(".f12-form-validate").each(function () {
            // Check the validation options
            var validation = $(this).attr("validation");

            if (typeof(validation) === "undefined" || validation.length <= 0) {
                if (c.is_textarea($(this)) == false) {
                    return;
                } else {
                    // Custom case for textarea in wordpress
                    validation = JSON.stringify({"validation": {"required": true}});
                }
            }

            if (c.validate(e, $(this), JSON.parse(validation)) != true) {
                e.preventDefault();
            }
        });
    };

    /**
     * Check condition of the validation
     * returns true if all conditions are fullfilled.
     */
    this.check_condition = function (jQDOM, args) {
        var ret = true;
        for (var i = 0; i < args.length; i++) {
            var element;
            switch(args[i].type){
                case 'checkbox':
                    element = $(document).find("[name='" + args[i].name + "']:checked");
                    //console.log(element.val()+":"+args[i].value);
                    if(element.val() != args[i].value){
                        ret = false;
                    }
                    break;
                case 'text':
                default:
                    element = $(document).find("[name='" + args[i].name + "']");
                    //console.log(element.val()+":"+args[i].value);
                    if(element.val() != args[i].value){
                        ret = false;
                    }
                    break;
            }
        }
        return ret;
    };

    /**
     * run the validation
     */
    this.validate = function (e, jQDOM, args) {
        // Reset the error message
        this.reset_error_message(jQDOM);

        jQDOM.parents("td").removeClass("error");

        // Check the conditions
        if (typeof(args.condition) !== "undefined") {
            // Check condition
            if (!this.check_condition(jQDOM, args.condition)) {
                console.log("condition");
                return true;
            }
        }

        if (typeof(args.validation) === "undefined") {
            return true;
        }
        args = args.validation;

        for (var key in args) {
            if (args.hasOwnProperty(key)) {
                switch (key) {
                    case 'required':
                        if (!this.validate_required(jQDOM, args)) {
                            this.display_error_message(jQDOM, key, "Dieses Feld ist ein Pflichtfeld");
                            return false;
                        }
                        break;
                    case 'minlength':
                        if (!this.validate_minlength(jQDOM, args)) {
                            this.display_error_message(jQDOM, key, "Sie müssen mindestens " + args.minlength + " Zeichen eingeben.");
                            return false;
                        }
                        break;
                    case 'maxlength':
                        if (!this.validate_maxlength(jQDOM, args)) {
                            this.display_error_message(jQDOM, key, "Sie dürfen maximal " + args.maxlength + " Zeichen eingeben.");
                            return false;
                        }
                        break;
                    case 'not':
                        if (!this.validate_not(jQDOM, args)) {
                            this.display_error_message(jQDOM, key, "Das Feld darf nicht " + args.not + " sein.");
                            return false;
                        }
                        break;
                    case 'equal':
                        if (!this.validate_equal(jQDOM, args)) {
                            this.display_error_message(jQDOM, key, "Das Feld muss " + args.equal + " sein.");
                            return false;
                        }
                        break;
                }
                console.log(jQDOM.attr("name") + " - " + key + " : true");
            }
        }
        return true;
    };

    this.reset_error_message = function (jQDOM) {
        jQDOM.parent().find(".error-message").remove();
    };

    this.display_error_message = function (jQDOM, key, message) {
        console.log(jQDOM.attr("name") + " - " + key + " : false");
        jQDOM.parents("td:first").addClass("error");

        if (jQDOM.parents("td:first").find(".error-message").length == 0) {
            jQDOM.parents("td:first").append("<div class='error-message'></div>");
        }

        jQDOM.parents("td:first").find(".error-message").append("<p>" + message + "</p>");

        if ($(".f12-validation-error-message").length == 0) {
            $(".wp-header-end").after(
                "<div class=\"f12-validation-error-message notice is-dismissible notice-error\" style=\"display: block;\">\n" +
                "\t<p>Einige Felder erfüllen nicht die Vorraussetzungen. Bitte überprüfen Sie Ihre Eingaben.</p>\n" +
                "\t\n" +
                "\t<button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Diese Meldung ausblenden.</span></button></div>");
        }
    };

    this.get_type = function (jQDOM) {
        var type = jQDOM.attr("type");

        if (typeof(type) !== "undefined") {
            switch (type) {
                case 'radio':
                    return 'radio';
                case 'file':
                    return "file";
                case 'number':
                case 'text':
                    return "text";
                case 'checkbox':
                    return "checkbox";
                case 'hidden':
                    return "hidden";
            }
        } else {
            var nodename = jQDOM[0].nodeName.toLowerCase();
            if (nodename == "select") {
                return "select";
            } else if (nodename == "textarea") {
                return "textarea";
            }
        }
        return "undefined";
    };

    this.validate_equal = function (jQDOM, args) {
        switch (this.get_type(jQDOM)) {
            case "hidden":
            case "text":
                if (jQDOM.val() == args.equal) {
                    return true;
                }
                break;
            case "checkbox":
                return true;
            case "select":
                if (jQDOM.val() == args.equal) {
                    return true;
                }
                break;
            case "textarea":
                if (tinyMCE.get(jQDOM.attr("id")).getContent() == args.equal) {
                    return true;
                }
                break;
            default:
                console.log("undefined value");
                return true;
        }
        return false;
    };

    this.validate_not = function (jQDOM, args) {
        switch (this.get_type(jQDOM)) {
            case "hidden":
            case "text":
                if (jQDOM.val() != args.not) {
                    return true;
                }
                break;
            case "checkbox":
                return true;
            case "select":
                if (jQDOM.val() != args.not) {
                    return true;
                }
                break;
            case "textarea":
                if (tinyMCE.get(jQDOM.attr("id")).getContent() != args.not) {
                    return true;
                }
                break;
            default:
                console.log("undefined value");
                return true;
        }
        return false;
    };

    this.validate_minlength = function (jQDOM, args) {
        switch (this.get_type(jQDOM)) {
            case "hidden":
            case "text":
                if (jQDOM.val().length > args.minlength) {
                    return true;
                }
                break;
            case "checkbox":
                return true;
            case "select":
                return true;
            case "textarea":
                if (tinyMCE.get(jQDOM.attr("id")).getContent().length > args.minlength) {
                    return true;
                }
                break;
            default:
                console.log("undefined value");
                return true;
        }
        return false;
    };

    this.validate_maxlength = function (jQDOM, args) {
        switch (this.get_type(jQDOM)) {
            case "hidden":
            case "text":
                if (jQDOM.val().length < args.maxlength) {
                    return true;
                }
                break;
            case "checkbox":
                return true;
            case "select":
                return true;
            case "textarea":
                if (tinyMCE.get(jQDOM.attr("id")).getContent().length < args.maxlength) {
                    return true;
                }
                break;
            default:
                console.log("undefined value");
                return true;
        }
        return false;
    };

    this.validate_required = function (jQDOM, args) {
        switch (this.get_type(jQDOM)) {
            case "file":
                if(jQDOM[0].files.length > 0){
                    return true;
                }
                break;
            case "radio":
                var element = jQDOM.parents("form:first").find("[name="+jQDOM.attr("name")+"]:checked");
                if(element.length > 0){
                    return true;
                }
                break;
            case "hidden":
            case "text":
                if (jQDOM.val().length > 0) {
                    return true;
                }
                break;
            case "checkbox":
                if (jQDOM.is(":checked")) {
                    return true;
                }
                break;
            case "select":
                if (jQDOM.val() != -1) {
                    return true;
                }
                break;
            case "textarea":
                if (tinyMCE.get(jQDOM.attr("id")).getContent().length > 0) {
                    return true;
                }
                break;
            default:
                console.log("undefined value");
                return true;
        }
        return false;
    };

    /**
     * Call the constructor
     */
    this.init(jQueryObject);
}

$.fn.F12FormValidate = function () {
    new F12FormValidate(this);
};

$(document).ready(function () {
    $("form#post").F12FormValidate();
});