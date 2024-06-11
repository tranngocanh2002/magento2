<?php
namespace Magento\GiftCard\Model\ResourceModel\CustomerGift;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'customer_id';

    protected function _construct()
    {
        $this->_init(Magento\GiftCard\Model\CustomerGift::class, Magento\GiftCard\Model\ResourceModel\CustomerGift::class);
    }
}
