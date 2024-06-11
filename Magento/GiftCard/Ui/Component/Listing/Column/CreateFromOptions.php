<?php
namespace Magento\GiftCard\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

class CreateFromOptions implements OptionSourceInterface
{
    public function __construct(CollectionFactory $collectionFactory)
    {
        die(__METHOD__);
        $this->collectionFactory = $collectionFactory;
    }
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        die(__METHOD__);
        return [
            ['value' => 'option1_value', 'label' => __('Option 1')],
            ['value' => 'option2_value', 'label' => __('Option 2')],
            // Add more options as needed
        ];
    }
}
