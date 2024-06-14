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
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class CheckConnection
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class CheckConnection extends AbstractManageProfiles
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * CheckConnection constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param Data $helperData
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        Data $helperData
    ) {
        $this->helperData = $helperData;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $protocol = $this->getRequest()->getParam('protocol');
        $host = $this->getRequest()->getParam('host');
        $passive = $this->getRequest()->getParam('passive');
        $user = $this->getRequest()->getParam('user');
        $pass = $this->getRequest()->getParam('pass');
        $result = $this->helperData->checkConnection($protocol, $host, $passive, $user, $pass);

        return $this->getResponse()->representJson($result);
    }
}
