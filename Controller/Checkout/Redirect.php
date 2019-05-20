<?php
/**
 * @project    : YabanPay-Magento2
 * @description:
 * @user       : persi
 * @email persi@sixsir.com
 * @date       : 2018/9/1
 * @time       : 11:42
 */

namespace YaBandPay\Payment\Controller\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\View\Result\PageFactory;
use Magento\Payment\Helper\Data as PaymentHelper;
use YaBandPay\Payment\Controller\Controller;
use YaBandPay\Payment\Helper\General as YaBandWechatPayHelper;
use YaBandPay\Payment\Logger\Logger;

class Redirect extends Controller
{
    /**
     * Redirect constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param PageFactory $resultPageFactory
     * @param PaymentHelper $paymentHelper
     * @param YaBandWechatPayHelper $yaBandWechatPayHelper
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        PaymentHelper $paymentHelper,
        YaBandWechatPayHelper $yaBandWechatPayHelper,
        Logger $logger
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->paymentHelper = $paymentHelper;
        $this->yaBandWechatPayHelper = $yaBandWechatPayHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute Redirect to Mollie after placing order
     */
    public function execute()
    {
        try{
            $order = $this->checkoutSession->getLastRealOrder();
            if(!$order){
                $msg = __('Order not found.');
                $this->yaBandWechatPayHelper->addTolog('error', $msg);
                $this->_redirect('checkout/cart');
                return;
            }
            $payment = $order->getPayment();
            if(!isset($payment) || empty($payment)){
                $this->yaBandWechatPayHelper->addTolog('error', 'Order Payment is empty');
                $this->_redirect('checkout/cart');
                return;
            }
            $method = $order->getPayment()->getMethod();
            $methodInstance = $this->paymentHelper->getMethodInstance($method);
            if($methodInstance instanceof \YaBandPay\Payment\Model\AbstractPayment){
                $redirectUrl = $methodInstance->startTransaction($order);
                /**
                 * @var Http $response
                 */
                $response = $this->getResponse();
                $response->setRedirect($redirectUrl);
            }else{
                $msg = __('Paymentmethod not found.');
                $this->messageManager->addErrorMessage($msg);
                $this->yaBandWechatPayHelper->addTolog('error', $msg);
                $this->checkoutSession->restoreQuote();
                $this->_redirect('checkout/cart');
            }
        }catch(\Exception $e){
            $this->messageManager->addExceptionMessage(
                $e, __($e->getMessage())
            );
            $this->yaBandWechatPayHelper->addTolog('error', $e->getMessage());
            $this->checkoutSession->restoreQuote();
            $this->_redirect('checkout/cart');
        }
    }
}
