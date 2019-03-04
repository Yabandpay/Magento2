<?php

namespace YaBandPay\Payment\Model;

/**
 * Class WechatPay
 * @package YaBandPay\Payment\Model
 * @description
 * @version 1.0.0
 */
class WechatPay extends AbstractPayment
{
    const CODE = 'yabandpay_wechatpay';

    protected $_code = self::CODE;

}
