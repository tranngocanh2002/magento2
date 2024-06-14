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

namespace Mageplaza\OrderExport\Plugin\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Store\Model\Store;
use Mageplaza\CronSchedule\Model\Job as CronJob;
use Mageplaza\OrderExport\Helper\Data;

/**
 * Class Job
 * @package Mageplaza\OrderExport\Plugin\Cron
 */
class Job
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
     * @param CronJob $config
     * @param $schedule
     *
     * @return array
     */
    public function beforeExecuteJob(CronJob $config, $schedule)
    {
        $jobCode = $schedule->getJobCode();
        if (str_contains($jobCode, 'mp_order_export_id_')) {
            $scheduleData = Data::jsonEncode(['job_code' => $jobCode]);
            $this->writer->save(
                'mageplaza_order_export_cron_schedule_info',
                $scheduleData,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            );
        }

        return [$schedule];
    }
}
