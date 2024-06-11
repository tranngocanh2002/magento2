<?php
namespace Magento\GiftCard\Model\ResourceModel\Gift;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'giftcard_id';

    protected function _construct()
    {
        $this->_init(\Magento\GiftCard\Model\Gift::class, \Magento\GiftCard\Model\ResourceModel\Gift::class);
    }

    public function abcd(){
        return '1234';
    }

}
