<?php
/**
 * @project: Magento2.3.0
 * @description:
 * @user: PersiLiao
 * @email: persi@sixsir.com
 * @date: 2019/2/20
 * @time: 17:15
 */

namespace YaBandPay\Payment\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use YaBandPay\Payment\Helper\General as YaBandWechatPayHelper;
use YaBandPay\Payment\Model\WechatPay;
use YaBandPay\PersiLiao\Cryptography;
use YaBandPay\PersiLiao\Notify;

abstract class Api extends Controller
{
    /**
     * Notify constructor.
     * @param Context $context
     * @param PaymentHelper $paymentHelper
     * @param YaBandWechatPayHelper $yaBandWechatPayHelper
     */
    public function __construct(
        Context $context,
        WechatPay $payment,
        PaymentHelper $paymentHelper,
        YaBandWechatPayHelper $yaBandWechatPayHelper
    )
    {
        $this->resultFactory = $context->getResultFactory();
        $this->paymentHelper = $paymentHelper;
        $this->yaBandWechatPayHelper = $yaBandWechatPayHelper;
        $this->paymentInstance = $payment;
        parent::__construct($context);
    }

    protected function getOrderInfo()
    {
        $notify = new Notify(
            new Cryptography(
                $this->yaBandWechatPayHelper->getApiToken()
            )
        );
        return $notify->getOrderInfo();
    }
}