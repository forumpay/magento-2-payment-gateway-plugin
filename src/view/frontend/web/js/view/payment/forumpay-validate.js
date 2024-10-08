define(
  ['Magento_Checkout/js/model/quote'],
  function (quote) {
    'use strict';
    return {
      validate: function () {
        const selectedPaymentMethod = quote.paymentMethod()?.method ?? null;

        if (selectedPaymentMethod === null) {
          throw new Error('Payment method is not defined.');
        }

        if (selectedPaymentMethod === 'forumpay') {
          return window.forumPayPaymentGatewayWidget.validate(selectedPaymentMethod);
        }
      }
    };
  }
);
