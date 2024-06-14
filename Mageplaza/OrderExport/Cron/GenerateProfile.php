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

namespace Mageplaza\OrderExport\Cron;

use Exception;
use Magento\Framework\App\Config\Value;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Config\Source\Events;
use Mageplaza\OrderExport\Model\HistoryFactory;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class GenerateProfile
 * @package Mageplaza\OrderExport\Cron
 */
class GenerateProfile
{
    /**
     * @var Value
     */
    private $config;

    /**
     * @var ProfileFactory
     */
    private $profileFactory;

    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * GenerateProfile constructor.
     *
     * @param Value $config
     * @param ProfileFactory $profileFactory
     * @param HistoryFactory $historyFactory
     * @param Data $helperData
     */
    public function __construct(
        Value $config,
        ProfileFactory $profileFactory,
        HistoryFactory $historyFactory,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->config = $config;
        $this->profileFactory = $profileFactory;
        $this->historyFactory = $historyFactory;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        if (!$this->helperData->isEnabled()) {
            return;
        }
        $config = $this->config->load('mageplaza_order_export_cron_schedule_info', 'path');
        $cron = Data::jsonDecode($config->getValue());

        if (isset($cron['job_code'])) {
            $id = str_replace('mp_order_export_id_', '', $cron['job_code']);
            $profile = $this->profileFactory->create()->load($id);
            if ($profile->getId()) {
                if (!$profile->getStatus()) {
                    return;
                }
                $history = $this->historyFactory->create();
                $generateMsg = '';
                try {
                    $this->helperData->generateProfile($profile);
                    $generate = Events::GENERATE_SUCCESS;
                } catch (Exception $e) {
                    $generate = Events::GENERATE_ERROR;
                    $generateMsg = $e->getMessage();
                }
                $generateStt = $generate === Events::GENERATE_SUCCESS;
                $history->setData([
                    'profile_id' => $profile->getId(),
                    'name' => $profile->getName(),
                    'generate_status' => $generateStt ? 'Success' : 'Error',
                    'type' => 'Cron',
                    'file' => $generateStt ? $profile->getLastGeneratedFile() : '',
                    'product_count' => $generateStt ? $profile->getLastGeneratedProductCount() : 0,
                    'message' => $generateMsg
                ]);
                $delivery = Events::DELIVERY_DISABLE;
                if ($generate === Events::GENERATE_SUCCESS) {
                    $deliveryResult = $this->processDeliveryTab($profile, $history);
                    if ($deliveryResult === null) {
                        $delivery = Events::DELIVERY_DISABLE;
                    } elseif ($deliveryResult) {
                        $delivery = Events::DELIVERY_SUCCESS;
                    } else {
                        $delivery = Events::DELIVERY_ERROR;
                    }
                }
                $history->save();
                $profile->sendAlertMail($generate, $delivery);
            }
        }
    }

    /**
     * @param $profile
     * @param $history
     *
     * @return bool|null
     */

    protected function processDeliveryTab($profile, $history)
    {
        $success = null;
        $deliveryMsg = null;

        try {
            if ($profile->getUploadEnable()) {
                $this->helperData->deliveryProfile($profile);
                $success = true;
            }
            if ($profile->getEmailEnable()) {
                $profile->sendExportedFileViaMail();
                $success = true;
            }
            if ($profile->getHttpEnable()) {
                $result = $this->helperData->sendHttpRequest($profile);
                if ($result["success"]) {
                    $success = true;
                } else {
                    $success = false;
                    $deliveryMsg = $result['message'];
                }
            }
        } catch (Exception $e) {
            $success = false;
            $deliveryMsg = $e->getMessage();
        }
        if ($success !== null) {
            $deliveryStatus = $success ? "Success" : "Error";
            $history->setDeliveryStatus($deliveryStatus)->setMessage($deliveryMsg);
        }

        return $success;
    }
}
