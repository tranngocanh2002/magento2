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

namespace Mageplaza\OrderExport\Plugin\Cron\Model;

use Magento\Cron\Model\Config as CronConfig;
use Mageplaza\OrderExport\Cron\GenerateProfile;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Config
 * @package Mageplaza\OrderExport\Plugin\Cron\Model
 */
class Config
{
    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * Config constructor.
     *
     * @param ProfileFactory $profileFactory
     */
    public function __construct(ProfileFactory $profileFactory)
    {
        $this->profileFactory = $profileFactory;
    }

    /**
     * @param CronConfig $config
     * @param array $result
     *
     * @return mixed
     */
    public function afterGetJobs(CronConfig $config, $result)
    {
        $collection = $this->profileFactory->create()->getCollection();
        /** @var Profile $profile */
        foreach ($collection as $profile) {
            if (!$profile->getCronSchedule()) {
                continue;
            }
            $cronSchedule = trim($profile->getCronSchedule() ?: '');
            if ($cronSchedule && $profile->getData('auto_run') && count(explode(' ', $cronSchedule)) === 5) {
                $result['default']['mp_order_export_id_' . $profile->getId()] = [
                    'name'     => 'mp_order_export_id_' . $profile->getId(),
                    'instance' => GenerateProfile::class,
                    'method'   => 'execute',
                    'schedule' => $cronSchedule
                ];
            }
        }

        return $result;
    }
}
