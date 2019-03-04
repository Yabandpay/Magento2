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
    public function addInfoLog($type, $data)
    {
        if (is_array($data)) {
            $this->addInfo($type . ': ' . json_encode($data));
        } elseif (is_object($data)) {
            $this->addInfo($type . ': ' . json_encode($data));
        } else {
            $this->addInfo($type . ': ' . $data);
        }
    }

    /**
     * Add error data to mollie Log
     *
     * @param $type
     * @param $data
     */
    public function addErrorLog($type, $data)
    {
        if (is_array($data)) {
            $this->addError($type . ': ' . json_encode($data));
        } elseif (is_object($data)) {
            $this->addError($type . ': ' . json_encode($data));
        } else {
            $this->addError($type . ': ' . $data);
        }
    }
}
