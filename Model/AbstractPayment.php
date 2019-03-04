<?php

namespace YaBandPay\Payment\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;
use YaBandPay\Payment\Helper\General as YaBandWechatPayHelper;
use YaBandPay\PersiLiao\Payment;

/**
 * Class AbstractPayment
 * @package YaBandPay\Payment\Model
 * @description
 * @version 1.0.0
 */
abstract class AbstractPayment extends AbstractMethod
{
    /**
     * Enable Initialize
     *
     * @var bool
     */
    protected $_isInitializeNeeded = true;
    /**
     * Enable Gateway
     *
     * @var bool
     */
    protected $_isGateway = true;
    /**
     * Enable Refund
     *
     * @var bool
     */
    protected $_canRefund = true;
    /**
     * Enable Partial Refund
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    protected $_canUseCheckout = true;

    protected $_canCapture = true;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var YaBandWechatPayHelper
     */
    private $yaBandWechatPayHelper;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Order
     */
    private $order;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var OrderSender
     */
    private $orderSender;
    /**
     * @var InvoiceSender
     */
    private $invoiceSender;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Mollie constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param ObjectManagerInterface $objectManager
     * @param YaBandWechatPayHelper $yaBandWechatPayHelper
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Order $order
     * @param OrderSender $orderSender
     * @param InvoiceSender $invoiceSender
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ObjectManagerInterface $objectManager,
        YaBandWechatPayHelper $yaBandWechatPayHelper,
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        Order $order,
        OrderSender $orderSender,
        InvoiceSender $invoiceSender,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->objectManager = $objectManager;
        $this->yaBandWechatPayHelper = $yaBandWechatPayHelper;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->order = $order;
        $this->orderSender = $orderSender;
        $this->invoiceSender = $invoiceSender;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initialize($paymentAction, $stateObject)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getInfoInstance();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);

        $status = $this->yaBandWechatPayHelper->getStatusPending();
        $stateObject->setState(Order::STATE_NEW);
        $stateObject->setStatus($status);
        $stateObject->setIsNotified(false);
    }

    /**
     * startTransaction
     * @param Order $order
     * @return string
     */
    public function startTransaction(Order $order)
    {
        try{
            $orderPayUrl = $this->yaBandWechatPayHelper->getOrderPayUrl($order->getPayment()->getMethod(), $order);
            if(empty($orderPayUrl)){
                return $this->yaBandWechatPayHelper->getCheckoutUrl();
            }
            $message = __('Customer redirected to YaBandPay, url: %1', $orderPayUrl);
            $status = $this->yaBandWechatPayHelper->getStatusPending();
            $order->addStatusToHistory($status, $message, false);
            $order->save();
            return $orderPayUrl;
        }catch(\Exception $e){
            $this->yaBandWechatPayHelper->addTolog('error', $e->getMessage());
            return $this->yaBandWechatPayHelper->getCheckoutUrl();
        }
    }

    /**
     * processTransaction
     * @param array $orderInfo
     * @return array
     */
    public function processTransaction(array $orderInfo)
    {
        try{
            $order = $this->order->load($orderInfo['order_id']);
            if(empty($order)){
                $msg = [ 'error' => true, 'msg' => __('Order not found') ];
                $this->yaBandWechatPayHelper->addTolog('error', $msg);
                return $msg;
            }
            $status = $orderInfo['state'];
            if($order->getState() && $status == YaBandWechatPayHelper::PAY_PAID){
                if(!$order->getIsVirtual()){
                    $defaultStatusProcessing = $this->yaBandWechatPayHelper->getStatusProcessing();
                    if($defaultStatusProcessing && ($defaultStatusProcessing != $order->getStatus())){
                        $order->setStatus($defaultStatusProcessing)
                            ->setData(Payment::META_TRANSACTION_ID, $orderInfo['transaction_id'])
                            ->setData(Payment::META_TRADE_ID, $orderInfo['trade_id'])
                            ->save();
                    }
                }

                $msg = [ 'success' => true, 'status' => 'paid', 'order_id' => $orderInfo['order_id'] ];
                $this->yaBandWechatPayHelper->addTolog('success', $msg);
                return $msg;
            }

            $msg = [ 'success' => false, 'status' => $status, 'order_id' => $orderInfo['order_id'] ];
            return $msg;
        }catch(\Exception $e){
            $msg = [ 'error' => true, 'msg' => $e->getMessage() ];
            $this->yaBandWechatPayHelper->addTolog('error', $msg);
            return $msg;
        }
    }
}
