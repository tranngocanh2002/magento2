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

namespace Mageplaza\OrderExport\Block\Adminhtml\ProfileExportOptions;

use Mageplaza\OrderExport\Block\Adminhtml\ProfileExportOptions;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class Shipment
 * @package Mageplaza\OrderExport\Block\Adminhtml\ProfileExportOptions
 */
class Shipment extends ProfileExportOptions
{
    const TYPE = Profile::TYPE_SHIPMENT;
}
