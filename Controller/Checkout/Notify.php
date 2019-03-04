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

use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use YaBandPay\Payment\Controller\Api;
use YaBandPay\PersiLiao\Response;

class Notify extends Api
{
    /**
     * Execute Redirect to Mollie after placing order
     */
    public function execute()
    {
        /**
         * @var Raw $result
         */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setHeader('content-type', 'text/plain');
        try{
            $orderInfo = $this->getOrderInfo();
            if($orderInfo){
                $this->paymentInstance->processTransaction($orderInfo);
                $result->setContents(Response::OK);
            }else{
                $result->setContents(Response::BAD);
            }
        }catch(\Exception $e){
            $this->messageManager->addExceptionMessage(
                $e, __($e->getMessage())
            );
            $this->yaBandWechatPayHelper->addTolog('error', 'Notify:' . $e->getMessage());
            $result->setContents(Response::BAD);
        }
        return $result;
    }
}
