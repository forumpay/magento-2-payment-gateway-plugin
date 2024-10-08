require([
    'jquery',
    'mage/url',
    'domReady!',
], function ($, urlBuilder) {
    'use strict';

    let baseUrl = urlBuilder.build('');
    if (!baseUrl) {
        const protocol = window.location.protocol;
        const host = window.location.host;
        baseUrl = protocol + "//" + host + "/rest/V1/forumpay/syncPayment";
    }

    const paymentIdElement = $('.order_payment_id');
    const paymentId = paymentIdElement.text();

    const syncButton = $('#forumpay_api_sync_payment');

    function applyMargin() {
        if ($(window).width() < 776) {
            syncButton.css('margin-bottom', '30px');
        } else {
            syncButton.css('margin-bottom', '0');
        }
    }

    applyMargin();

    $(window).resize(function() {
        applyMargin();
    });

    $('.order_payment_reference').css('margin', '30px 0 10px 0');

    syncButton.on('click', function(e) {
        e.preventDefault();
        var $button = $(this);
        var originalText = $button.text();
        $button.prop('disabled', true);
        $button.text('Syncing ...');
        $.ajax({
            url: baseUrl,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                paymentId: paymentId,
            }),
            showLoader: true,
            beforeSend: function (xhr) {
                //Empty to remove magento's default handler
            },
            success: function(response) {
                $button.prop('disabled', false);
                $button.text(originalText);
                alert('Order status updated to: ' + response?.status);
                window.location.reload();
            },
            error: function(error) {
                $button.prop('disabled', false);
                $button.text(originalText);
                var message = "Unknown error occurred. Please contact support.";
                alert(message);
            }
        });
    });
});
