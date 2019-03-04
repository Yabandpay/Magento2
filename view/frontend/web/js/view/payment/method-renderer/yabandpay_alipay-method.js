/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        // 'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component, url) {
        'use strict';
        return Component.extend({
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'YaBandPay_Payment/payment/pay'
            },
            getMethodImage: function () {
                return window.checkoutConfig.image[this.item.method];
            },
            getTitle: function () {
                return window.checkoutConfig.payment.yabandpay_alipay.title;
            },
            afterPlaceOrder: function () {
                window.location.href = url.build('yabandpay/checkout/redirect/');
            }
        });
    }
);
