define([
  "uiComponent",
  "Magento_Checkout/js/model/payment/renderer-list",
], function (Component, rendererList) {
  "use strict";
  rendererList.push({
    type: "forumpay",
    component:
      "ForumPay_PaymentGateway/js/view/payment/method-renderer/forumpay-method",
  });

  return Component.extend({});
});
