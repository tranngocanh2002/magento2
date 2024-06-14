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

use Mageplaza\OrderExport\Model\Config\AbstractSource;

/**
 * Class ExportType
 * @package Mageplaza\OrderExport\Model\Config\Source
 */
class ExportType extends AbstractSource
{
    const LOOP_ORDER = 1;
    const UN_LOOP_ORDER = 0;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::LOOP_ORDER => __('Duplicate Order'),
            self::UN_LOOP_ORDER => __('Not Duplicate Order'),

        ];
    }
}
