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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Model\DefaultTemplateFactory;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class LoadTemplate
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class LoadTemplate extends AbstractManageProfiles
{
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var DefaultTemplateFactory
     */
    protected $defaultTemplate;

    /**
     * LoadTemplate constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param JsonHelper $jsonHelper
     * @param DefaultTemplateFactory $defaultTemplate
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        JsonHelper $jsonHelper,
        DefaultTemplateFactory $defaultTemplate
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->defaultTemplate = $defaultTemplate;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $name = $this->_request->getParam('name');
        $defaultTemplate = $this->defaultTemplate->create()->load($name, 'name');

        return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($defaultTemplate));
    }
}
