<?php
namespace Magento\GiftCard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerGift extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('customer_giftcard', 'customer_giftcard_id');
    }
}
