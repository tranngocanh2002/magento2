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

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;

/**
 * Class Preview
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class ResetFlag extends AbstractManageProfiles
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $profile = $this->initProfile();
        if ($profile->getId()) {
            $profile->setExportedIds('')->save();

            return $this->getResponse()->representJson(true);
        }

        return $this->getResponse()->representJson(false);
    }
}
