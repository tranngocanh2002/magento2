<?php
namespace Magento\GiftCard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GiftCardHistory extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('giftcard_history', 'history_id');
    }
}
