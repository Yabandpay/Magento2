<?php
/**
 * @project    : YabanPay-Magento2
 * @description:
 * @user       : persi
 * @email persi@sixsir.com
 * @date       : 2018/9/5
 * @time       : 00:14
 */

namespace YaBandPay\Payment\Model\Config\Source;


class Currency
{
    public function toOptionArray()
    {
        $currency = [
            'CNY' => 'CNY',
            'EUR' => 'EUR'
        ];

        $options = [ [ 'value' => '', 'label' => __('-- Please Select --') ] ];
        foreach($currency as $code => $label){
            $options[] = [ 'value' => $code, 'label' => $label ];
        }
        return $options;
    }
}