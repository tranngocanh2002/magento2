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
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab\General;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Config\Source\Events;
use Mageplaza\OrderExport\Model\HistoryFactory;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Generate
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class Generate extends AbstractManageProfiles
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
     * @var Layout
     */
    protected $layout;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * Generate constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param HistoryFactory $historyFactory
     * @param JsonFactory $jsonFactory
     * @param Layout $layout
     * @param Data $helperData
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        HistoryFactory $historyFactory,
        JsonFactory $jsonFactory,
        Layout $layout,
        Data $helperData
    ) {
        $this->historyFactory = $historyFactory;
        $this->helperData     = $helperData;
        $this->layout         = $layout;
        $this->jsonFactory    = $jsonFactory;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $profile    = $this->initProfile(true);
        $success    = null;
        $errorMsg   = '';
        $generate   = Events::GENERATE_DISABLE;

        try {
            $result            = $this->helperData->processRequest($profile);
            $result['success'] = true;
            $success           = true;
            if (isset($result['complete'])
                && $result['complete']
                && $this->getRequest()->getParam('step') === 'render'
            ) {
                $generate               = Events::GENERATE_SUCCESS;
                $result['general_html'] = $this->layout->createBlock(General::class)->toHtml();
                $result['object_count'] = $profile->getLastGeneratedProductCount();
                $this->historyFactory->create()->addData([
                    'profile_id'      => $profile->getId(),
                    'name'            => $profile->getName(),
                    'generate_status' => $success ? 'Success' : 'Error',
                    'type'            => 'Manual',
                    'file'            => $success ? $profile->getLastGeneratedFile() : '',
                    'product_count'   => $success ? $profile->getLastGeneratedProductCount() : 0,
                    'message'         => $errorMsg
                ])->save();
            }
        } catch (Exception $e) {
            $generate = Events::GENERATE_ERROR;
            $result   = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        $profile->sendAlertMail($generate, Events::DELIVERY_DISABLE);

        return $resultJson->setData($result);
    }
}
