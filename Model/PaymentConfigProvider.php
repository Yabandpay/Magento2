<?php
/**
 * Copyright © 2018 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace YaBandPay\Payment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Helper\Data as PaymentHelper;
use function var_export;
use YaBandPay\Payment\Helper\General as YaBandWechatPayHelper;
use YaBandPay\Payment\Logger\Logger;
use YaBandPay\PersiLiao\Payment;

/**
 * Class PaymentConfigProvider
 *
 * @package YaBandPay\Payment\Model
 * @description
 * @version 1.0.0
 */
class PaymentConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Escaper
     */
    private $escaper;
    /**
     * @var AssetRepository
     */
    private $assetRepository;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var YaBandWechatPayHelper
     */
    private $yabandpayPaymentHelper;
    /**
     * @var Logger $logger
     */
    private $logger;

    /**
     * PaymentConfigProvider constructor.
     * @param PaymentHelper $paymentHelper
     * @param CheckoutSession $checkoutSession
     * @param AssetRepository $assetRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Escaper $escaper
     * @param YaBandWechatPayHelper $yabandpayPaymentHelper
     * @param Logger $logger
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        CheckoutSession $checkoutSession,
        AssetRepository $assetRepository,
        ScopeConfigInterface $scopeConfig,
        Escaper $escaper,
        YaBandWechatPayHelper $yabandpayPaymentHelper,
        Logger $logger
    )
    {
        $this->paymentHelper = $paymentHelper;
        $this->checkoutSession = $checkoutSession;
        $this->escaper = $escaper;
        $this->assetRepository = $assetRepository;
        $this->scopeConfig = $scopeConfig;
        $this->yabandpayPaymentHelper = $yabandpayPaymentHelper;
        $this->logger = $logger;
    }


    /**
     * Config Data for checkout
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $activeWechatPay = $this->yabandpayPaymentHelper->getIsActiveWechatPay();
        if( $activeWechatPay === true){
            $config['payment'][WechatPay::CODE]['isActive'] = true;
            $config['payment'][WechatPay::CODE]['title'] = Payment::WECHAT . $this->yabandpayPaymentHelper->getWechatPayDesc();
        }else{
            $config['payment'][WechatPay::CODE]['isActive'] = false;
        }
        $activeAliPay = $this->yabandpayPaymentHelper->getIsActiveAlipay();

        if($activeAliPay === true){
            $config['payment'][AliPay::CODE]['isActive'] = true;
            $config['payment'][AliPay::CODE]['title'] = Payment::ALIPAY . $this->yabandpayPaymentHelper->getAlipayDesc();
        }else{
            $config['payment'][AliPay::CODE]['isActive'] = false;
        }

        return $config;
    }
}
