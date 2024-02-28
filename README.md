# ForumPay Crypto Payments for Magento 2
# Installation guide

## Requirements

> Make sure you have actual version of Magento installed (2.4 and higher).

> Install using the composer

## Installation using composer

```shell
composer require forumpay/magento-2-payment-gateway-plugin
```

### Now you need to use Magento CLI to enable extracted module:

1. Open command line and navigate to the root of magento installation
   For example: /var/www/html/magento/
2. Run following commands to activate the module:
   ```shell
   php bin/magento module:enable ForumPay_PaymentGateway
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento setup:static-content:deploy -f
   php bin/magento cache:flush
   ```

## Upgrade from previous version

If previous version has been installed manually by unzipping .zip archive, then we need to remove all files from the file
system before we install new version using the composer.
Locate the **module** directory, usually in to the **/app/code** directory **relative** to your **magento** root.
It should look like this: **/app/code/Limitlex/ForumPay**.
Remove entire Limitlex folder with all sub-folders and files.

Follow [Installation using composer](#installation-using-composer) section.

When plugin is installed and activate, you need set new webhook url following [How to set up webhook](#how-to-set-up-webhook).

## Configuration

Open magento admin panel and go to:
**STORES** -> **Configuration** -> **Sales** -> **Payment Methods**

Scroll down until you find ForumPay dropdown. Open it.

Enable module by setting **Enabled** to '**Yes**'.

### What each field does:

1. **Title**
   The label of the payment method that is displayed when user is prompted to choose one. You can leave default or set it to something like *Pay with crypto*.
2. **Environment**
   Dropdown lets you switch between 'Production' and 'Sandbox' modes.
   Use 'Production' for processing real transactions in a live environment and
   'Sandbox' for safe testing without financial implications.
3. **API User / Merchant Id**
   This is our identifier that we need to access the payment system.
   It can be found in your **Profile**.
   [Go to profile >](https://dashboard.forumpay.com/pay/userPaymentGateway.api_settings)
4. **API Secret**
   _Important:_ never share it to anyone!
   Think of it as a password.
   API Secret consists of two parts. When generated in [ForumPay dashboard](https://dashboard.forumpay.com/pay/userPaymentGateway.api_settings),
   the first one will be displayed in your profile, while the second part will be sent to your e-mail.
   You need to enter both parts here (one after the other).
5. **POS Id**
   This is how payments coming to your wallets are going to be identified.
   Special characters are not allowed. Allowed characters are: `[A-Za-z0-9._-]` (e.g. `my-shop`, `my_shop`).
6. **Accept Instant (Zero) Confirmations**
   Allows immediate transaction approval without waiting for network confirmations, enhancing speed but with increased risk.
7. **New order status**
    Which status the order gets when user starts the payment.
8. **Order Status After Payment Captured**
    Which status the order gets once user successfully completes the payment.
9. **Instructions**
    Instructions that are going to be displayed for user during the process of placing order.
10. **Sort order**
    Where the payment method must be placed inside the list of payment methods.
    0 = First position, 1 = Second, 2 = Third ...
11. **Payment Icon**
    Icon that is going to be displayed when we ask user to select payment method.
12. **Custom environment URL**
    Optional: URL to the API server. This value will override the default setting. Only used for debugging.

Don't forget to hit save button after fields are filled.
Magento may also ask you to refresh cache.
So if you see something like: '*One or more of the Cache Types are invalidated*',
just follow instructions.

## Webhook setup

**Webhook** allows us to check order status **independently** of user actions.

For example, if user **closes tab** after payment is started, we cannot determine what the status of order is.

Is it **Cancelled** or is it **Confirmed**?
In our case it will be *Pending Payment* **forever**.

### This is where webhook comes in.

If webhook is set up in your [Profile](https://dashboard.forumpay.com/pay/userPaymentGateway.api_settings#webhook_notifications), it holds the url where it **calls back** on transaction status change.

Now, when user transfers his money and closes the tab before transaction is confirmed (~2 min), we loose state as before.

But now after payment is confirmed, webhook tells our webshop to check order status. And we get proper state of order.

### How to set up webhook

Go to your [Profile](https://dashboard.forumpay.com/pay/userPaymentGateway.api_settings#webhook_notifications) and scroll down until you find Webhook URL.

Insert **URL** in this field:
`YOUR_WEBSHOP/rest/V1/forumpay/webhook`

Where **YOUR_WEBSHOP** is the URL of your webshop. An example of the complete webhook URL would be:
`https://my.webshop.com/rest/V1/forumpay/webhook`

## Functionality

Now ForumPay payment method must be available during order checkout.

User has to select **ForumPay** and then choose the cryptocurrency.

When currency is selected, block with transaction data will be displayed.
(Amount, Rate, Fee, Total, Expected time).

After currency selection user also has to fill default fields like
First/Last Name, County-City-Address-Code and his Phone Number.
(These fields are customizable in Magento and aren't related to the plugin).

When user clicks on **Place order** button he is being redirected to the payment view, at this moment transaction is being created and it waits for money transfer.

Then user has 5 minutes to pay the order by scanning the **QR Code** or manually using address shown under the QR Code.

## Logging

Logs live in **/var/log** relative to your magento installation root directory.
