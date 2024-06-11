<?php
namespace Magento\GiftCard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GiftCard extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('giftcard_code', 'giftcard_id');
    }
}
