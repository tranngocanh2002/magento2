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
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Edit
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class Edit extends AbstractManageProfiles
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        /** @var Profile $profile */
        $profile = $this->initProfile();
        if (!$profile) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('mporderexport/manageprofiles/index');

            return $resultRedirect;
        }
        if ($this->getRequest()->isAjax() && $this->getRequest()->getParam('reset_exported') && $profile->getId()) {
            $profile->setExportedIds(null)->save();

            return null;
        }
        $data = $this->_session->getData('mageplaza_orderexport_profile', true);
        if (!empty($data)) {
            $profile->setData($data);
        }

        $this->coreRegistry->register('mageplaza_orderexport_profile', $profile);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_OrderExport::manage_profiles');
        $resultPage->getConfig()->getTitle()->set(__('Profile'));
        $title = $profile->getId() ? __('Edit %1 profile', $profile->getName()) : __('New Profile');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
