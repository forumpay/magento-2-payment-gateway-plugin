define([
  'ko',
  'jquery',
  'Magento_Checkout/js/model/url-builder',
  'Magento_Checkout/js/view/payment/default',
  'ForumPay_PaymentGateway/js/get-rate-widget',
], function (ko, $, urlBuilder, Component, getRateWidget) {
  "use strict";

  return Component.extend({
    defaults: {
      redirectAfterPlaceOrder: false,
      template: "ForumPay_PaymentGateway/payment/forumpay",
    },
    getData: function () {
      return {
        method: this.item.method,
        additional_data: {
          payment_gateway: "forumpay",
        },
      };
    },
    getInstructions: function () {
      return window.checkoutConfig.payment.forumpay.instructions;
    },
    getPaymentMethodMarkSrc: function () {
      return window.checkoutConfig.payment.forumpay.paymentMethodImage;
    },
    isImageVisible: function () {
      return window.checkoutConfig.payment.forumpay.isImageVisible ? 'true' : 'hidden';
    },
    afterPlaceOrder: function () {
      if ($('body').hasClass('ForumPayPaymentGatewayWidgetBody')) {
        window.forumPayPaymentGatewayWidget.startPayment();
        $('#ForumPayPaymentGatewayWidgetActionsToolbar').hide();
        return;
      }

      $('body').addClass('ForumPayPaymentGatewayWidgetBody');
      var element = $('#ForumPayPaymentGatewayWidgetContainer').detach();
      var toolbar = $('#ForumPayPaymentGatewayWidgetActionsToolbar').detach();

      $('#checkout').after(element);
      element.after(toolbar);
      toolbar.hide();

      window.forumPayPaymentGatewayWidget.startPayment();
      return false;
    },
    initGetRateWidget: function () {
      getRateWidget();
    },
  });
});
