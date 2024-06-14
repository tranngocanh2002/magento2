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

namespace Mageplaza\OrderExport\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Log
 * @package Mageplaza\OrderExport\Controller\Adminhtml\Logs
 */
class Log extends AbstractManageProfiles
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Log constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * Profile generate log
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->initProfile(true);

        return $this->resultLayoutFactory->create();
    }
}
