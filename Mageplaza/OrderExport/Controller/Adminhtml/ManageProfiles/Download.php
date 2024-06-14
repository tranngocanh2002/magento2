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
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Generate
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class Download extends AbstractManageProfiles
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var
     */
    protected $redirectFactory;

    /**
     * Download constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param Data $helperData
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        FileFactory $fileFactory,
        Data $helperData
    ) {
        $this->fileFactory = $fileFactory;
        $this->helperData = $helperData;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('file_name');
        if ($fileName) {
            return $this->fileFactory->create(
                $fileName,
                ['type' => 'filename', 'value' => 'mageplaza/order_export/profile/' . $fileName],
                'media'
            );
        }
        $profile = $this->initProfile();
        if (!$profile->getId()) {
            $this->messageManager->addErrorMessage(__('Profile no longer exits'));

            return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
        }

        $lastGeneratedFile = $profile->getLastGeneratedFile();

        try {
            return $this->fileFactory->create(
                $profile->getLastGeneratedFile(),
                ['type' => 'filename', 'value' => 'mageplaza/order_export/profile/' . $lastGeneratedFile],
                'media'
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something wrong when download file: %1', $e->getMessage()));

            return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
        }
    }
}
