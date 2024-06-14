<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderExport
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderExport\Observer;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\PreOrder\Helper\Item;
use Mageplaza\PreOrder\Model\Config\Source\OrderTypeInGrid;

/**
 * Class CronJobRun
 * @package Mageplaza\OrderExport\Observer
 */
class CronJobRun implements ObserverInterface
{
    /**
     * @var Writer
     */
    private $writer;

    /**
     * Schedule constructor.
     *
     * @param Writer $writer
     */
    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $jobName = $observer->getData('job_name');
        if (str_contains($jobName, 'mp_order_export_id_')) {
            $jobCode      = substr($jobName, strpos($jobName, 'mp_order_export_id_'));
            $scheduleData = Data::jsonEncode(['job_code' => $jobCode]);
            $this->writer->save(
                'mageplaza_order_export_cron_schedule_info',
                $scheduleData,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            );
        }
    }
}
