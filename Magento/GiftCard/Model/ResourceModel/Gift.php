<?php
namespace Magento\GiftCard\Model\ResourceModel;

class Gift extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('giftcard_code', 'giftcard_id');
    }

}
