(function ($) {
    $.fn.applyMust = function () {
        $(".vd_date_required").datepicker({
            showOn: 'button',
            changeMonth: true,
            changeYear: true,
            yearRange: "c-50:c",
            dateFormat: 'dd/mm/yy',
        /*    buttonImage: __webPath__.replace('?','nicresources/img/icon_cal.png?'),
            buttonImageOnly: true*/
        });
        $(".vd_date").datepicker({
            showOn: 'button',
            changeMonth: true,
            changeYear: true,
            yearRange: "c-50:c",
            dateFormat: 'dd/mm/yy',
            /*buttonImage: __webPath__.replace('?','nicresources/img/icon_cal.png?'),
            buttonImageOnly: true*/
        });
        $.validator.addClassRules({
            vd_date: {
                dateITA: true
            },
            vd_date_required: {
                dateITA: true,
                required: true
            },
            vd_digits_required: {
                required: true,
                digits: true
            },
            vd_email_required: {
                required: true,
                email: true
            },
            vd_email: {
                email: true
            },
            vd_mobile_required: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            vd_mobile: {
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            vd_code: {
                minlength: 2,
                maxlength: 11
            },
            vd_code_required: {
                required: true,
                minlength: 2,
                maxlength: 11
            },
            vd_name: {
                minlength: 3,
                maxlength: 100
            },
            vd_name_required: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            vd_address: {
                minlength: 5,
                maxlength: 200
            },
            vd_address_required: {
                required: true,
                minlength: 5,
                maxlength: 200
            },
            vd_longitude_required: {
                required: true,
                digits: true,
                range: [-180, 180]
            },
            vd_latitude_required: {
                required: true,
                digits: true,
                range: [-90, 90]
            }

        });

        $.validator.setDefaults({ignore: ":hidden:not(select)"});
        $('form').validate({
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.is('select')) {
                    error.insertBefore(element);
                } else
                {
                    error.insertAfter(element);
                }
            }
        });

        return this;
    };
}(jQuery));