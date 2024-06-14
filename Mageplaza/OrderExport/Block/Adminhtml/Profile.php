<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderExport
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderExport\Block\Adminhtml;

use Magento\Backend\Block\Widget\Button\SplitButton;
use Magento\Backend\Block\Widget\Container;

/**
 * Class Profile
 * @package Mageplaza\OrderExport\Block\Adminhtml
 */
class Profile extends Container
{
    /**
     * Prepare button and grid
     *
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id'           => 'add_new_profile',
            'label'        => __('Add New'),
            'class'        => 'add',
            'button_class' => '',
            'class_name'   => SplitButton::class,
            'options'      => $this->_getAddProductButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Profile' split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = [
            'order'      => [
                'label'   => __('Order'),
                'onclick' => $this->getOnClickAction('order'),
                'default' => true,
            ],
            'invoice'    => [
                'label'   => __('Invoice'),
                'onclick' => $this->getOnClickAction('invoice'),
            ],
            'creditmemo' => [
                'label'   => __('Creditmemo'),
                'onclick' => $this->getOnClickAction('creditmemo'),
            ],
            'shipment'   => [
                'label'   => __('Shipment'),
                'onclick' => $this->getOnClickAction('shipment'),
            ]
        ];

        return $splitButtonOptions;
    }

    /**
     * @param $type
     *
     * @return string
     */
    protected function getOnClickAction($type)
    {
        return "setLocation('" . $this->getUrl('mporderexport/manageprofiles/new', ['type' => $type]) . "')";
    }
}
