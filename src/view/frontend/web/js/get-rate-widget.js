define([
    'jquery',
    'forumPayWidget',
    'Magento_Customer/js/customer-data'
  ], function ($, ForumPayWidget, customerData) {
    return function () {
      const config = {
        baseUrl: window.checkoutConfig.payment.forumpay.baseUrl,
        restGetCryptoCurrenciesUri: '/rest/V1/forumpay/currency',
        restGetRateUri: 'rest/V1/forumpay/getRate',
        restStartPaymentUri: '/rest/V1/forumpay/startPayment',
        restCheckPaymentUri: '/rest/V1/forumpay/checkPayment',
        restCancelPaymentUri: '/rest/V1/forumpay/cancelPayment',
        successResultUrl: window.checkoutConfig.payment.forumpay.successResultUrl,
        errorResultUrl: window.checkoutConfig.payment.forumpay.errorResultUrl,
        messageReceiver: function (name, data) {
          switch (name) {
            case 'PAYMENT_CANCELED':
              customerData.reload();
              break;
            case 'PAYMENT_RETRY':
              $('#ForumPayPaymentGatewayWidgetActionsToolbar').show();
              break;
          }
        }
      }
      window.forumPayPaymentGatewayWidget = new ForumPayPaymentGatewayWidget(config);

      window.forumPayPaymentGatewayWidget.init();
    }
  }
)
