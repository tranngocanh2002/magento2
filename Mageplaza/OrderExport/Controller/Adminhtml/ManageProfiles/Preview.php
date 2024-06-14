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

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Preview
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class Preview extends AbstractManageProfiles
{
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Preview constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param JsonHelper $jsonHelper
     * @param Data $helperData
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        FileFactory $fileFactory,
        JsonHelper $jsonHelper,
        Data $helperData
    ) {
        $this->fileFactory = $fileFactory;
        $this->jsonHelper  = $jsonHelper;
        $this->helperData  = $helperData;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $data    = $this->getRequest()->getParam('profile');
        $profile = $this->profileFactory->create();
        if (isset($data['id']) && ($id = $data['id'])) {
            $profile = $profile->load($id);
        }
        if (isset($data['fields_list']) && $data['fields_list']) {
            $data['fields_list'] = $this->jsonHelper->jsonEncode($data['fields_list']);
        }
        $profile->addData($data);
        list($content, $ids) = $this->helperData->generateLiquidTemplate($profile, [], 1);
        $this->helperData->createProfileFile('preview', $content);

        return $this->fileFactory->create(
            'preview.' . $this->helperData->getFileType($profile->getFileType()),
            ['type' => 'filename', 'value' => 'mageplaza/order_export/profile/preview', 'rm' => true],
            'media'
        );
    }
}
