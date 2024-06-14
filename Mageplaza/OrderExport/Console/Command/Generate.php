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

namespace Mageplaza\OrderExport\Console\Command;

use Exception;
use Magento\Framework\App\State;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Config\Source\Events;
use Mageplaza\OrderExport\Model\HistoryFactory;
use Mageplaza\OrderExport\Model\ProfileFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Generate
 * @package Mageplaza\OrderExport\Console\Command
 */
class Generate extends Command
{
    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var State
     */
    protected $state;

    /**
     * Generate constructor.
     *
     * @param State $state
     * @param ProfileFactory $profileFactory
     * @param HistoryFactory $historyFactory
     * @param Data $helperData
     * @param null $name
     */
    public function __construct(
        State $state,
        ProfileFactory $profileFactory,
        HistoryFactory $historyFactory,
        Data $helperData,
        $name = null
    ) {
        $this->state          = $state;
        $this->profileFactory = $profileFactory;
        $this->historyFactory = $historyFactory;
        $this->helperData     = $helperData;

        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('orderexport:generate')
            ->setDescription('OrderExport Generate Profiles');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->getAreaCode();
        } catch (Exception $e) {
            $this->state->setAreaCode('adminhtml');
        }
        $collection     = $this->profileFactory->create()->getCollection();
        $profileUpdated = 0;
        foreach ($collection as $profile) {
            if (!$profile->getStatus()) {
                continue;
            }
            $history = $this->historyFactory->create();
            try {
                $this->helperData->generateProfile($profile);
                $generate = Events::GENERATE_SUCCESS;
                $logMsg   = __('Profile %1 has been generated successfully', $profile->getName());
                $output->writeln("<info>{$logMsg}</info>");
            } catch (Exception $e) {
                $generate = Events::GENERATE_ERROR;
                $logMsg   = __('Something went wrong while generating profile %1', $profile->getName());
                $output->writeln("<error>{$logMsg}</error>");
            }
            $historyData = [
                'profile_id'      => $profile->getId(),
                'name'            => $profile->getName(),
                'generate_status' => $generate === Events::GENERATE_SUCCESS ? __('Success') : __('Error'),
                'type'            => 'Command',
                'file'            => $generate === Events::GENERATE_SUCCESS ? $profile->getLastGeneratedFile() : '',
                'product_count'   => $generate === Events::GENERATE_SUCCESS ?
                    $profile->getLastGeneratedProductCount() : 0,
            ];
            $history->setData($historyData);

            if ($generate === Events::GENERATE_SUCCESS) {
                $profileUpdated++;
                $this->processDeliveryTab($profile, $output, $history);
            }
            $history->save();
        }
        if ($profileUpdated) {
            $output->writeln("<info>" . __('A total of %1 record(s) have been updated.', $profileUpdated) . "</info>");
        }
    }

    /**
     * @param $profile
     * @param $output
     * @param $history
     */

    public function processDeliveryTab($profile, $output, $history)
    {
        $success     = null;
        $deliveryMsg = null;
        $logType     = 'info';
        $logMsg      = __('Profile %1 has been uploaded successfully', $profile->getName());

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
                if ($result['success']) {
                    $success = true;
                } else {
                    $success     = false;
                    $deliveryMsg = $result['message'];
                    $logType     = 'error';
                    $logMsg      = __(
                        'Something went wrong while sending http request in profile %1. %2',
                        $profile->getName(),
                        $deliveryMsg
                    );
                }
            }
        } catch (Exception $e) {
            $success     = false;
            $deliveryMsg = $e->getMessage();
            $logType     = 'error';
            $logMsg      = __(
                'Something went wrong while processing profile %1. %2',
                $profile->getName(),
                $deliveryMsg
            );
        }
        if ($success !== null) {
            $deliveryStatus = $success ? 'Success' : 'Error';
            $history->setDeliveryStatus($deliveryStatus)->setMessage($deliveryMsg);

            $log = $logType === 'info' ? "<info>{$logMsg}</info>" : "<error>{$logMsg}</error>";
            $output->writeln($log);
        }
    }
}
