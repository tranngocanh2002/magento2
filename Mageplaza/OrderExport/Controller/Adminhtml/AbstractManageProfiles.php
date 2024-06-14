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

namespace Mageplaza\OrderExport\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class AbstractManageProfiles
 * @package Mageplaza\OrderExport\Controller\Adminhtml
 */
abstract class AbstractManageProfiles extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageplaza_OrderExport::manage_profiles';

    /**
     * Profile model factory
     *
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * AbstractManageProfiles constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->profileFactory = $profileFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|Profile
     */
    protected function initProfile($register = false)
    {
        $profileId = $this->getRequest()->getParam('id');
        /** @var Profile $profile */
        $profile = $this->profileFactory->create();

        if ($profileId) {
            $profile = $profile->load($profileId);
            if (!$profile->getId()) {
                $this->messageManager->addErrorMessage(__('The profile no longer exists.'));

                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('mageplaza_orderexport_profile', $profile);
        }

        return $profile;
    }
}
