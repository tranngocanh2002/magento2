<?php
namespace Magento\GiftCard\Model\ResourceModel\GiftCard;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magento\GiftCard\Model\GiftCard', 'Magento\GiftCard\Model\ResourceModel\GiftCard');
    }
}
