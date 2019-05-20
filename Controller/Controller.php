<?php
/**
 * @project    : YabanPay-Magento2
 * @description:
 * @user       : persi
 * @email persi@sixsir.com
 * @date       : 2018/9/1
 * @time       : 13:49
 */

namespace YaBandPay\Payment\Controller;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Payment\Helper\Data;
use YaBandPay\Payment\Logger\Logger;

abstract class Controller extends Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Data
     */
    protected $paymentHelper;
    /**
     * @var \YaBandPay\Payment\Helper\General
     */
    protected $yaBandWechatPayHelper;
    /**
     * @var \YaBandPay\Payment\Model\AbstractPayment
     */
    protected $paymentInstance;
    /**
     * @var Logger $logger
     */
    protected $logger;
}