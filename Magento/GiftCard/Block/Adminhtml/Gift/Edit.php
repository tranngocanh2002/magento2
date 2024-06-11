<?php
namespace Magento\GiftCard\Block\Adminhtml\Gift;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magento_GiftCard';
        $this->_controller = 'adminhtml_gift';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Gift Card'));
        $this->addButton(
            'save_and_edit_button',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ]
        );
    }

    public function getHeaderText()
    {
        return __('Edit Gift Card');
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit']);
    }
}
