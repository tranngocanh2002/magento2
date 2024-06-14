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

namespace Mageplaza\OrderExport\Model\Config\Source;

use Magento\Sales\Model\Order\Invoice;
use Mageplaza\OrderExport\Model\Config\AbstractSource;

/**
 * Class InvoiceState
 * @package Mageplaza\OrderExport\Model\Config\Source
 */
class InvoiceState extends AbstractSource
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            Invoice::STATE_OPEN => 'Open',
            Invoice::STATE_PAID => 'Paid',
            Invoice::STATE_CANCELED => 'Canceled',
        ];
    }
}
