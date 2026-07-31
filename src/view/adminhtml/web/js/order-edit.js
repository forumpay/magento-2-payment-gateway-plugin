require([
    'jquery',
    'domReady!',
], function ($) {
    'use strict';

    const syncButton = $('#forumpay_api_sync_payment');
    const baseUrl = syncButton.attr('data-sync-url');
    if (!baseUrl) {
        return;
    }

    const paymentIdElement = $('.order_payment_id');
    const paymentId = paymentIdElement.text();

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
            dataType: 'json',
            data: {
                form_key: window.FORM_KEY,
                paymentId: paymentId,
            },
            showLoader: true,
            success: function(response) {
                $button.prop('disabled', false);
                $button.text(originalText);
                if (response?.order_status_changed) {
                    alert('Order status updated to: ' + response?.status);
                } else {
                    alert('No updates');
                }
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
