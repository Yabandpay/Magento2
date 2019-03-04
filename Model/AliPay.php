<?php

namespace YaBandPay\Payment\Model;

/**
 * Class AliPay
 * @package YaBandPay\Payment\Model
 * @description
 * @version 1.0.0
 */
class AliPay extends AbstractPayment
{
    const CODE = 'yabandpay_alipay';

    protected $_code = self::CODE;
}
