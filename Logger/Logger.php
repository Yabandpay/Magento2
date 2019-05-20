<?php
/**
 * @project: YabanPay-Magento2
 * @description:
 * @user: persi
 * @email persi@sixsir.com
 * @date: 2018/8/31
 * @time: 20:57
 */

namespace YaBandPay\Payment\Logger;

class Logger extends \Monolog\Logger
{

    /**
     * Add info data to Mollie Log
     *
     * @param $type
     * @param $data
     */
    public function addInfoLog($data)
    {
        if(is_array($data) || is_object($data)){
            $this->addInfo(json_encode($data));
        }else{
            $this->addInfo($data);
        }
    }

    /**
     * Add error data to mollie Log
     *
     * @param $type
     * @param $data
     */
    public function addErrorLog($data)
    {
        if(is_array($data) || is_object($data)){
            $this->addError(json_encode($data));
        }else{
            $this->addError($data);
        }
    }
}
