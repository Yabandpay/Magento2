/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        console.log(window.checkoutConfig);

        if (window.checkoutConfig.payment.yabandpay_wechatpay.isActive) {
            rendererList.push(
                {
                    type: 'yabandpay_wechatpay',
                    component: 'YaBandPay_Payment/js/view/payment/method-renderer/yabandpay_wechatpay-method'
                }
            );
        }

        if (window.checkoutConfig.payment.yabandpay_alipay.isActive) {
            rendererList.push(
                {
                    type: 'yabandpay_alipay',
                    component: 'YaBandPay_Payment/js/view/payment/method-renderer/yabandpay_alipay-method'
                }
            );
        }

        /** Add view logic here if needed */
        return Component.extend({});
    }
);