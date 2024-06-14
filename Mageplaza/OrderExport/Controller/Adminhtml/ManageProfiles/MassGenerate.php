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

namespace Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Config\Source\Events;
use Mageplaza\OrderExport\Model\History;
use Mageplaza\OrderExport\Model\HistoryFactory;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ResourceModel\Profile\CollectionFactory;

/**
 * Class MassGenerate
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class MassGenerate extends Action
{
    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    public $filter;

    /**
     * Collection Factory
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * MassGenerate constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Data $helperData
     * @param HistoryFactory $historyFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Data $helperData,
        HistoryFactory $historyFactory
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->helperData        = $helperData;
        $this->historyFactory    = $historyFactory;

        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection     = $this->filter->getCollection($this->collectionFactory->create());
        $profileUpdated = 0;

        foreach ($collection as $profile) {
            if ($profile->getStatus()) {
                $history = $this->historyFactory->create();
                $logMsg  = '';

                try {
                    $this->helperData->generateProfile($profile);
                    $generate = Events::GENERATE_SUCCESS;
                    $this->messageManager->addSuccessMessage(
                        __('Profile %1 has been generated successfully', $profile->getName())
                    );
                } catch (Exception $e) {
                    $generate  = Events::GENERATE_ERROR;
                    $logMsg   .= __('Something went wrong while generating Profile %1', $profile->getName());
                    $this->messageManager->addErrorMessage($logMsg);
                }

                $productCount = $generate === Events::GENERATE_SUCCESS ? $profile->getLastGeneratedProductCount() : 0;

                $history->setData([
                    'profile_id'      => $profile->getId(),
                    'name'            => $profile->getName(),
                    'generate_status' => $generate === Events::GENERATE_SUCCESS ? 'Success' : 'Error',
                    'type'            => 'Manual',
                    'file'            => $generate === Events::GENERATE_SUCCESS ? $profile->getLastGeneratedFile() : '',
                    'product_count'   => $productCount,
                    'message'         => $logMsg
                ]);
                $delivery = Events::DELIVERY_DISABLE;
                if ($generate === Events::GENERATE_SUCCESS) {
                    $profileUpdated++;
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
                if ($profile->getSender() || $this->helperData->getEmailConfig('send_to')) {
                    $profile->sendAlertMail($generate, $delivery);
                }
            }
        }

        if ($profileUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $profileUpdated));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param Profile $profile
     * @param History $history
     *
     * @return bool|null
     */
    protected function processDeliveryTab($profile, $history)
    {
        $success     = null;
        $deliveryMsg = null;
        $logMsg      = '';

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
            $logMsg      = __(
                'Something went wrong while processing profile %1. %2',
                $profile->getName(),
                $deliveryMsg
            );
        }
        if ($success !== null) {
            $deliveryStatus = $success ? 'Success' : 'Error';
            $history->setDeliveryStatus($deliveryStatus)->setMessage($deliveryMsg);
            $success
                ? $this->messageManager->addSuccessMessage(__(
                    'Profile %1 has been delivered successfully',
                    $profile->getName()
                ))
                : $this->messageManager->addErrorMessage($logMsg);
        }

        return $success;
    }
}
