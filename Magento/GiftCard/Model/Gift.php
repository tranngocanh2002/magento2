<?php
namespace Magento\GiftCard\Model;
class Gift extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Magento\GiftCard\Model\ResourceModel\Gift::class);
    }

    public function getAcbd(){
        return 123;
    }
}
