require([
    'jquery',
    'mage/url',
    'domReady!',
    'mage/validation',
], function ($, urlBuilder) {
    'use strict';

    let baseUrl = urlBuilder.build('');
    if (!baseUrl) {
        const protocol = window.location.protocol;
        const host = window.location.host;
        baseUrl = protocol + "//" + host + "/rest/V1/forumpay/ping";
    }

    const fieldsLabel = [
        $('[id^=row_payment][id$=_forumpay_accept_underpayment_threshold]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_underpayment_modify_order_total]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_underpayment_modify_order_total_description]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_overpayment_modify_order_total]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_overpayment_modify_order_total_description]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_ping_button]').find('td.label').find('span'),
    ];

    const fieldsToValidate = [
        {
            title: 'validate-underpay-threshold',
            triggerFieldId: $('[id^=payment][id$=_forumpay_accept_underpayment]'),
            targetFieldId: $('[id^=payment][id$=_forumpay_accept_underpayment_threshold]'),
            errorMessage: 'This field is required when "Enable to automatically accept underpayments" is set to "Yes".',
        },
        {
            title: 'validate-underpay-description',
            triggerFieldId: $('[id^=payment][id$=_forumpay_accept_underpayment_modify_order_total]'),
            targetFieldId: $('[id^=payment][id$=_forumpay_accept_underpayment_modify_order_total_description]'),
            errorMessage: 'This field is required when "Enable to modify the order total to reflect underpayment" is set to "Yes".',
        },
        {
            title: 'validate-overpay-description',
            triggerFieldId: $('[id^=payment][id$=_forumpay_accept_overpayment_modify_order_total]'),
            targetFieldId: $('[id^=payment][id$=_forumpay_accept_overpayment_modify_order_total_description]'),
            errorMessage: 'This field is required when "Enable to modify the order total to reflect overpayment" is set to "Yes".',
        },
    ];

    function toggleFieldsVisibility(show, fieldsToToggle) {
        fieldsToToggle.forEach(function (field) {
            if (show) {
                field.show();
            } else {
                field.hide();
            }
        });
    }

    toggleFieldsVisibility(false, fieldsLabel);

    $('#config-edit-form').validate({
        errorClass: 'mage-error',
        validClass: 'mage-valid',
        submitHandler: function (form) {
            let isValid = true;

            fieldsToValidate.forEach(function (field) {
                const $targetField = field.targetFieldId;
                if (!$targetField.valid()) {
                    isValid = false;
                }
            });

            if (isValid) {
                form.submit();
            }
        }
    });

    fieldsToValidate.forEach(function (field) {
        $.validator.addMethod(
            field.title,
            function (value) {
                const selected = field.triggerFieldId.val();
                return !(selected === '1' && value.trim() === '');
            },
            $.mage.__(field.errorMessage)
        );
    });

    fieldsToValidate.forEach(function (field) {
        field.targetFieldId.rules(
            'add',
            {
                [field.title]: true
            }
        );
    });

    $('#payment_forumpay_api_test').on('click', function (e) {
        e.preventDefault();

        var $button = $(this);
        var originalText = $button.text();
        $button.prop('disabled', true);
        $button.text('Testing ...');

        // You can perform AJAX calls or other logic here
        $.ajax({
            url: baseUrl, // This is a global variable in admin that points to admin-ajax.php
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                apiEnv: $('[id^=payment][id$=_forumpay_payment_environment]').val(),
                apiKey: $('[id^=payment][id$=_forumpay_merchant_api_user]').val(),
                apiSecret: $('[id^=payment][id$=_forumpay_merchant_api_secret]').val(),
                apiUrlOverride: $('[id^=payment][id$=_forumpay_payment_environment_override]').val(),
            }),
            showLoader: true,
            beforeSend: function (xhr) {
                //Empty to remove magento's default handler
            },
            success: function (response) {
                $button.prop('disabled', false);
                $button.text(originalText);
                alert('Server responded: ' + response?.message);
            },
            error: function (error) {
                $button.prop('disabled', false);
                $button.text(originalText);
                const now = new Date();

                // Extract the UTC components
                const year = now.getUTCFullYear();
                const month = String(now.getUTCMonth() + 1).padStart(2, '0'); // Months are zero-indexed, so add 1
                const day = String(now.getUTCDate()).padStart(2, '0');
                const hours = String(now.getUTCHours()).padStart(2, '0');
                const minutes = String(now.getUTCMinutes()).padStart(2, '0');
                const seconds = String(now.getUTCSeconds()).padStart(2, '0');

                // Format the date and time in UTC
                const currentDateTimeUTC = `${year}-${month}-${day} ${hours}:${minutes}:${seconds} UTC`;

                var message = '';

                if (error?.responseJSON?.code > 0) {
                    message += error.responseJSON.code + ' - ';
                }

                message += error?.responseJSON?.message ?? "Unknown error occurred. Please contact support."

                message += "\n\n" + "Date: " + currentDateTimeUTC;
                if (error?.responseJSON?.cfray_id) {
                    message += "\n" + "Ray Id: " + error?.responseJSON?.cfray_id;
                }

                alert(message);
            }
        });
    });
});
