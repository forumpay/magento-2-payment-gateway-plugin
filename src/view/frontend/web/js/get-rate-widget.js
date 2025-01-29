define([
    'jquery',
    'forumPayWidget',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
  ], function ($, ForumPayWidget, customerData, quote, customer) {
    return function () {
      const customerInfo = quote.shippingAddress();
      const customerEmail = quote?.guestEmail ?? customer?.customerData?.email;

      const config = {
        baseUrl: window.checkoutConfig.payment.forumpay.baseUrl,
        restGetCryptoCurrenciesUri: '/rest/V1/forumpay/currency',
        restGetRateUri: 'rest/V1/forumpay/getRate',
        restStartPaymentUri: '/rest/V1/forumpay/startPayment',
        restCheckPaymentUri: '/rest/V1/forumpay/checkPayment',
        restCancelPaymentUri: '/rest/V1/forumpay/cancelPayment',
        restRestoreCart: '/rest/V1/forumpay/restoreCart',
        successResultUrl: window.checkoutConfig.payment.forumpay.successResultUrl,
        errorResultUrl: window.checkoutConfig.payment.forumpay.errorResultUrl,
        forumPayApiUrl: window.checkoutConfig.payment.forumpay.forumPayApiUrl,
        payer: {
          'payer_type': '',
          'payer_first_name': customerInfo?.firstname ?? '',
          'payer_last_name': customerInfo?.lastname ?? '',
          'payer_country_of_residence': customerInfo?.countryId ?? '',
          'payer_email': customerEmail ?? '',
          'payer_date_of_birth': '',
          'payer_country_of_birth': '',
          'payer_company': customerInfo?.company ?? '',
          'payer_date_of_incorporation': '',
          'payer_country_of_incorporation': customerInfo?.countryId ?? '',
        },
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
