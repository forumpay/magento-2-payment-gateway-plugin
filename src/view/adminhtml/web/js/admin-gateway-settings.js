require([
    'jquery',
    'domReady!',
    'mage/validation',
], function ($) {
    'use strict';

    const $testButton = $('#payment_forumpay_api_test');
    const baseUrl = $testButton.attr('data-ping-url');
    if (!baseUrl) {
        return;
    }

    const SECRET_MASK = '******';

    const $apiEnvField = $('[id^=payment][id$=_forumpay_payment_environment]');
    const $apiUrlOverrideField = $('[id^=payment][id$=_forumpay_payment_environment_override]');
    const $apiUserField = $('[id^=payment][id$=_forumpay_merchant_api_user]');
    const $apiSecretField = $('[id^=payment][id$=_forumpay_merchant_api_secret]');

    function normalizeUrl(url) {
        url = (url || '').trim();
        if (!url) {
            return '';
        }
        return url.replace(/\/+$/, '').toLowerCase();
    }

    function isSecretMask(value) {
        value = (value || '').trim();
        return value === SECRET_MASK || /^\*+$/.test(value);
    }

    function secretMissing() {
        const value = ($apiSecretField.val() || '').trim();
        return value === '' || isSecretMask(value);
    }

    const savedOverrideUrl = normalizeUrl(
        $testButton.attr('data-saved-override') || $apiUrlOverrideField.val()
    );
    const savedApiEnv = ($apiEnvField.val() || '').trim();

    const $credentialNotice = $(
        '<p id="forumpay-api-url-change-notice" class="note" style="display:none; color:#e22626;"></p>'
    );
    $apiSecretField.closest('td').append($credentialNotice);

    function overrideChanged() {
        return normalizeUrl($apiUrlOverrideField.val()) !== savedOverrideUrl;
    }

    function apiEnvChanged() {
        return ($apiEnvField.val() || '').trim() !== savedApiEnv;
    }

    function shouldBlockCredentialSave() {
        return (overrideChanged() || apiEnvChanged()) && secretMissing();
    }

    function updateCredentialNotice() {
        if (shouldBlockCredentialSave()) {
            $credentialNotice.text(
                'Enter your API Secret to change the API environment.'
            ).show();
            return;
        }
        $credentialNotice.hide();
    }

    function updateTestButtonState() {
        $testButton.prop('disabled', shouldBlockCredentialSave());
        updateCredentialNotice();
    }

    function blockSettingsSaveIfCredentialsMissing(e) {
        if (!shouldBlockCredentialSave()) {
            return;
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        updateCredentialNotice();
        $apiSecretField.focus();
        $('html, body').animate({ scrollTop: $apiSecretField.offset().top - 100 }, 200);
        return false;
    }

    $apiEnvField.on('change', updateTestButtonState);
    $apiUrlOverrideField.on('input change', updateTestButtonState);
    $apiSecretField.on('input change', updateTestButtonState);
    $('#config-edit-form').on('submit', blockSettingsSaveIfCredentialsMissing);
    updateTestButtonState();

    const fieldsLabel = [
        $('[id^=row_payment][id$=_forumpay_accept_underpayment_threshold]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_underpayment_modify_order_total]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_underpayment_modify_order_total_description]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_overpayment_threshold]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_overpayment_modify_order_total]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_accept_overpayment_modify_order_total_description]').find('td.label').find('span'),
        $('[id^=row_payment][id$=_forumpay_ping_button]').find('td.label').find('span'),
    ];

    const descriptionFields = [
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

    const thresholdFields = [
        {
            title: 'validate-underpay-threshold',
            triggerFieldId: $('[id^=payment][id$=_forumpay_accept_underpayment]'),
            targetFieldId: $('[id^=payment][id$=_forumpay_accept_underpayment_threshold]'),
            pattern: '^(?!0+(\\.0{1,2})?$)(\\d{1,2})(\\.\\d{1,2})?$',
            errorMessage: 'Please enter a valid percentage between 0 and 100 or leave blank to accept any underpayment amount.',
        },
        {
            title: 'validate-overpay-threshold',
            triggerFieldId: $('[id^=payment][id$=_forumpay_accept_overpayment]'),
            targetFieldId: $('[id^=payment][id$=_forumpay_accept_overpayment_threshold]'),
            pattern: '^(?!0+(\\.0{1,2})?$)(\\d{1,2})(\\.\\d{1,2})?$',
            errorMessage: 'Please enter a valid percentage between 0 and 100 or leave blank to accept any overpayment amount.',
        },
    ];

    const fieldsToValidate = [...descriptionFields, ...thresholdFields];

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

    const form = $('#config-edit-form');
    const shouldValidate = fieldsToValidate
        .map(field => field.triggerFieldId)
        .some(field => form.find(field).length > 0);

    if (shouldValidate) {
        form.validate({
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
    }

    fieldsToValidate.forEach(function (field) {
        $.validator.addMethod(
            field.title,
            function (value) {
                const selected = field.triggerFieldId.val();
                const fieldValue = value.trim();

                if (selected !== '1') {
                    return true;
                }

                if (thresholdFields.includes(field)) {
                    if (fieldValue === '') {
                        return true;
                    }
                    return new RegExp(field.pattern).test(value);
                }

                return fieldValue !== '';
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

    thresholdFields.forEach(function (field) {
        field.targetFieldId.on('blur', function () {
            this.value = this.value.trim();
            const value = this.value;

            if (!isNaN(value) && value !== '') {
                const floatValue = Number(value);
                const decimals = (value.split('.')[1] || '').length;

                if (decimals > 2) {
                    this.value = floatValue.toFixed(2);
                }
            }

            if (/^0\d+/.test(value) || parseFloat(value) === 0) {
                this.value = 0;
            }

            $(this).valid();
        });
    });

    const $feePaidBySelect = $('[id^=payment][id$=_forumpay_network_processing_fee_paid_by]');

    function toggleMerchantNotice() {
        const noticeId = $feePaidBySelect.attr('id') + '_merchant_notice';
        $('#' + noticeId).toggle($feePaidBySelect.val() === 'merchant');
    }

    $feePaidBySelect.on('change', toggleMerchantNotice);
    toggleMerchantNotice();

    $('#payment_forumpay_api_test').on('click', function (e) {
        e.preventDefault();

        if (shouldBlockCredentialSave()) {
            return;
        }

        var $button = $(this);
        var originalText = $button.text();
        $button.prop('disabled', true);
        $button.text('Testing ...');

        // You can perform AJAX calls or other logic here
        $.ajax({
            url: baseUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                form_key: window.FORM_KEY,
                apiEnv: $('[id^=payment][id$=_forumpay_payment_environment]').val(),
                apiKey: $apiUserField.val(),
                apiSecret: $apiSecretField.val(),
                apiUrlOverride: $apiUrlOverrideField.val(),
                webhookUrl: $('[id^=payment][id$=_forumpay_webhook_url]').val(),
            },
            showLoader: true,
            success: function (response) {
                updateTestButtonState();
                $button.text(originalText);

                const {webhook_success, webhook_ping_response, message} = response || {};
                const {status, duration, webhook_url, response_code, response_body} = webhook_ping_response || {};

                if (!webhook_success || !webhook_ping_response) {
                    alert(`Server responded: ${message}`);
                    return;
                }

                if (webhook_success === 'OK') {
                    alert(`Server responded: ${message}\n\nWebhook responded: ${webhook_success}`);
                    return;
                }

                alert(`Server responded: ${message}\n\nWebhook responded: ${webhook_success}
                    Status: ${status}
                    Duration: ${duration} seconds
                    Webhook URL: ${webhook_url}
                    ${response_code ? `Response Code: ${response_code}` : ''}
                    ${response_body ? `Response Body: ${response_body}` : ''}
                `);
            },
            error: function (error) {
                updateTestButtonState();
                $button.text(originalText);
                const now = new Date();

                const year = now.getUTCFullYear();
                const month = String(now.getUTCMonth() + 1).padStart(2, '0');
                const day = String(now.getUTCDate()).padStart(2, '0');
                const hours = String(now.getUTCHours()).padStart(2, '0');
                const minutes = String(now.getUTCMinutes()).padStart(2, '0');
                const seconds = String(now.getUTCSeconds()).padStart(2, '0');
                const currentDateTimeUTC = `${year}-${month}-${day} ${hours}:${minutes}:${seconds} UTC`;

                var message = '';

                if (error?.responseJSON?.code > 0) {
                    message += error.responseJSON.code + ' - ';
                }

                message += error?.responseJSON?.message ?? "Unknown error occurred. Please contact support."

                message += "\n\n" + "Date: " + currentDateTimeUTC;
                if (error?.responseJSON?.cfray_id) {
                    message += "\n" + "Ray Id: " + error.responseJSON.cfray_id;
                }

                alert(message);
            }
        });
    });
});
