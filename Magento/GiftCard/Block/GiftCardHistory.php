<?php
namespace Magento\GiftCard\Block;

use Magento\Framework\View\Element\Template;
use Magento\GiftCard\Model\ResourceModel\GiftCard\Collection;

class GiftCardHistory extends Template
{
    protected $giftCardCodeCollectionFactory;

    public function __construct(
        Template\Context $context,
        Collection $giftCardCodeCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->giftCardCodeCollectionFactory = $giftCardCodeCollectionFactory;
    }

    public function getGiftCardCodes()
    {
        $collection = $this->giftCardCodeCollectionFactory->create();
        $collection->addFieldToSelect('*'); // Lấy tất cả các trường
        return $collection;
    }
}

