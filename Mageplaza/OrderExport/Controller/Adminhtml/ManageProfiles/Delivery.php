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
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Config\Source\Events;
use Mageplaza\OrderExport\Model\HistoryFactory;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Generate
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class Delivery extends AbstractManageProfiles
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * Generate constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param HistoryFactory $historyFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param Data $helperData
     */
    public function __construct(
        ProfileFactory $profileFactory,
        HistoryFactory $historyFactory,
        Registry $coreRegistry,
        Context $context,
        Data $helperData
    ) {
        $this->helperData     = $helperData;
        $this->historyFactory = $historyFactory;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $profile        = $this->initProfile();
        $resultRedirect->setPath('mporderexport/*/edit', ['id' => $profile->getId(), '_current' => true]);
        if (!$profile->getUploadEnable() && !$profile->getEmailEnable() && !$profile->getHttpEnable()) {
            $this->messageManager->addErrorMessage(__('Please enable delivery'));

            return $resultRedirect;
        }

        $history     = $this->historyFactory->create();
        $deliveryMsg = '';
        try {
            if ($profile->getUploadEnable()) {
                $this->helperData->deliveryProfile($profile);
            }
            if ($profile->getEmailEnable()) {
                $profile->sendExportedFileViaMail();
            }

            $delivery = Events::DELIVERY_SUCCESS;
            if ($profile->getHttpEnable()) {
                $result = $this->helperData->sendHttpRequest($profile);
                if (!$result['success']) {
                    $delivery    = Events::DELIVERY_ERROR;
                    $deliveryMsg = $result['message'];
                }
            }
        } catch (Exception $e) {
            $delivery    = Events::DELIVERY_ERROR;
            $deliveryMsg = $e->getMessage();
        }

        if ($delivery === Events::DELIVERY_SUCCESS) {
            $this->messageManager->addSuccessMessage(__('The profile has been uploaded successfully'));
        } else {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while uploading the profile. %1', $deliveryMsg)
            );
        }

        $history->addData([
            'profile_id'      => $profile->getId(),
            'name'            => $profile->getName(),
            'delivery_status' => $delivery === Events::DELIVERY_SUCCESS ? 'Success' : 'Error',
            'type'            => 'Manual',
            'file'            => $profile->getLastGeneratedFile(),
            'product_count'   => $profile->getLastGeneratedProductCount(),
            'message'         => $deliveryMsg
        ])->save();

        if ($profile->getSender() || $this->helperData->getEmailConfig('send_to')) {
            $profile->sendAlertMail(Events::GENERATE_DISABLE, $delivery);
        }

        return $resultRedirect;
    }
}
