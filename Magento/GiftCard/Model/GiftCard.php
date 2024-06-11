<?php
namespace Magento\GiftCard\Model;

use Magento\Framework\Model\AbstractModel;

class GiftCard extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Magento\GiftCard\Model\ResourceModel\GiftCard::class);
    }
}
