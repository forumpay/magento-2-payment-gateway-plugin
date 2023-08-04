define([
  "uiComponent",
  "Magento_Checkout/js/model/payment/additional-validators",
  "ForumPay_PaymentGateway/js/view/payment/forumpay-validate",
], function (Component, additionalValidators, forumPayValidator) {
  "use strict";
  additionalValidators.registerValidator(forumPayValidator);
  return Component.extend({});
});
