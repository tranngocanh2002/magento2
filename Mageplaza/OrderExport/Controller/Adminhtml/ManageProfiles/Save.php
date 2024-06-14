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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Config\Source\Events;
use Mageplaza\OrderExport\Model\History;
use Mageplaza\OrderExport\Model\HistoryFactory;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class Save extends AbstractManageProfiles
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * Save constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param HistoryFactory $historyFactory
     * @param JsonHelper $jsonHelper
     * @param Data $helperData
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        HistoryFactory $historyFactory,
        JsonHelper $jsonHelper,
        Data $helperData
    ) {
        $this->historyFactory = $historyFactory;
        $this->jsonHelper     = $jsonHelper;
        $this->helperData     = $helperData;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->getRequest()->getPost('profile');
        $validate       = true;
        $conditionData  = $this->getRequest()->getPost('rule');
        $message        = '';

        if (isset($data['fields_list']) && is_string($data['fields_list'])) {
            $data['fields_list'] = '';
        }

        if (isset($data['fields_list']) && $data['fields_list']) {
            $data['fields_list'] = $this->jsonHelper->jsonEncode($data['fields_list']);
        }

        if (!array_key_exists('status_condition', $data)) {
            $data['status_condition'] = '';
        }

        if (isset($data['send_email_to']) && !empty($data['send_email_to'])) {
            $listEmailTo = explode(',', $data['send_email_to']);

            foreach ($listEmailTo as $emailTo) {
                if (!filter_var($emailTo, FILTER_VALIDATE_EMAIL)) {
                    $validate = false;
                    $message  = __('Invalid email format  in `Send Email To`');
                }
            }
        }

        if ($validate && $data['created_from'] && $data['created_to']
            && strtotime($data['created_from']) > strtotime($data['created_to'])) {
            $validate = false;
            $message  = __('Please enter Create Form value less than Create To value');
        }

        $profile = $this->initProfile();
        $profile->addData($data);
        $profile->loadPost($conditionData);

        if ($validate) {
            try {
                $profile->save();
                $this->messageManager->addSuccess(__('The profile has been saved.'));
                $this->_getSession()->setData('mageplaza_orderexport_profile_data', false);
                $type     = $this->getRequest()->getParam('type');
                $generate = Events::GENERATE_DISABLE;
                $delivery = Events::DELIVERY_DISABLE;
                if ($type == 'save_generate') {
                    try {
                        $this->helperData->generateProfile($profile);
                        $generate = Events::GENERATE_SUCCESS;
                        $stt      = 1;
                        $logMsg   = '';
                        $this->messageManager->addSuccessMessage(__('The profile has been generated successfully'));
                    } catch (Exception $e) {
                        $generate = Events::GENERATE_ERROR;
                        $stt      = 0;
                        $logMsg   = __('Something went wrong while generating the profile. %1', $e->getMessage());
                        $this->messageManager->addErrorMessage($logMsg);
                    }
                    $this->historyFactory->create()->addData([
                        'profile_id'      => $profile->getId(),
                        'name'            => $profile->getName(),
                        'generate_status' => $stt ? 'Success' : 'Error',
                        'type'            => 'Manual',
                        'file'            => $stt ? $profile->getLastGeneratedFile() : '',
                        'product_count'   => $stt ? $profile->getLastGeneratedProductCount() : 0,
                        'message'         => $logMsg
                    ])->save();
                } elseif ($type == 'save_generate_delivery') {
                    $history = $this->historyFactory->create();
                    try {
                        $this->helperData->generateProfile($profile);
                        $generate = Events::GENERATE_SUCCESS;
                        $logMsg   = '';
                        $this->messageManager->addSuccessMessage(__('The profile has been generated successfully'));
                    } catch (Exception $e) {
                        $logMsg = __('Something went wrong while generating the profile. %1', $e->getMessage());
                        $this->messageManager->addErrorMessage($logMsg);
                        $generate = Events::GENERATE_ERROR;
                    }
                    $generateStt = $generate === Events::GENERATE_SUCCESS;

                    $history->setData([
                        'profile_id'      => $profile->getId(),
                        'name'            => $profile->getName(),
                        'generate_status' => $generateStt ? 'Success' : 'Error',
                        'type'            => 'Manual',
                        'file'            => $generateStt ? $profile->getLastGeneratedFile() : '',
                        'product_count'   => $generateStt ? $profile->getLastGeneratedProductCount() : 0,
                        'message'         => $logMsg
                    ])->save();
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
                }

                $profile->sendAlertMail($generate, $delivery);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mporderexport/*/edit', ['id' => $profile->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('mporderexport/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Profile.'));
            }
        } else {
            $this->messageManager->addErrorMessage($message);
        }

        $this->_getSession()->setData('mageplaza_orderexport_profile_data', $data);

        $resultRedirect->setPath('mporderexport/*/edit', ['id' => $profile->getId(), '_current' => true]);

        return $resultRedirect;
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
            $this->messageManager->addErrorMessage($logMsg);
        }
        if ($success !== null) {
            $deliveryStatus = $success ? 'Success' : 'Error';
            $history->setDeliveryStatus($deliveryStatus)->setMessage($deliveryMsg);
            $success ?
                $this->messageManager->addSuccessMessage(
                    __('Profile %1 has been delivered successfully', $profile->getName())
                )
                : $this->messageManager->addErrorMessage($logMsg);
        }

        return $success;
    }
}
