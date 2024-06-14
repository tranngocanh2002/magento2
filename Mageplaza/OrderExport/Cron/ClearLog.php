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

namespace Mageplaza\OrderExport\Cron;

use Magento\Framework\App\ResourceConnection;
use Mageplaza\OrderExport\Helper\Data;

/**
 * Class ClearLog
 * @package Mageplaza\OrderExport\Cron
 */
class ClearLog
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * ClearLog constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param Data $helper
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Data $helper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->helper             = $helper;
    }

    /**
     * Clean Email Log after X day(s)
     *
     * @return $this
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $day = (int) $this->helper->getConfigGeneral('clean_log');

        if ($day) {
            $connection = $this->resourceConnection->getConnection();
            $table      = $this->resourceConnection->getTableName('mageplaza_orderexport_history');

            $connection->delete(
                $table,
                ['created_at < now() - interval ? DAY' => $day]
            );
        }

        return $this;
    }
}
