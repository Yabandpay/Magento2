<?php

namespace YaBandPay\Payment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use YaBandPay\Payment\Helper\General as YaBandWechatPayHelper;
use YaBandPay\PersiLiao\Payment;

class InstallData implements InstallDataInterface
{

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;
    /**
     * @var YaBandWechatPayHelper $yaBandWechatPayHelper
     */
    private $yaBandWechatPayHelper;
    /**
     * InstallData constructor.
     *
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        YaBandWechatPayHelper $yaBandWechatPayHelper

    )
    {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->yaBandWechatPayHelper = $yaBandWechatPayHelper;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create([ 'setup' => $setup ]);

        /**
         * Add 'mollie_transaction_id' attributes for order
         */
        $salesSetup->addAttribute('order', Payment::META_TRANSACTION_ID, array( 'type' => 'varchar', 'visible' => false, 'required' => false ));
        $salesSetup->addAttribute('order', Payment::META_TRADE_ID, array( 'type' => 'varchar', 'visible' => false, 'required' => false ));
    }
}
