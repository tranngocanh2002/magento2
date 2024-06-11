<?php
namespace Magento\GiftCard\Model\ResourceModel\GiftCardHistory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'history_id';

    protected function _construct()
    {
        $this->_init(Magento\GiftCard\Model\GiftCardHistory::class, Magento\GiftCard\Model\ResourceModel\GiftCardHistory::class);
    }
}
