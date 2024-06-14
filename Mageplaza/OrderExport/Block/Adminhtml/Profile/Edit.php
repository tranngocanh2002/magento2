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

namespace Mageplaza\OrderExport\Block\Adminhtml\Profile;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class Edit
 * @package Mageplaza\OrderExport\Block\Adminhtml\Profile
 */
class Edit extends Container
{
    /**
     * @var string
     */
    protected $_objectId = 'id';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Profile edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mageplaza_OrderExport';
        $this->_controller = 'adminhtml_profile';

        parent::_construct();
        $this->buttonList->remove('save');
        $this->buttonList->add('save-and-continue', [
            'label'          => __('Save and Continue Edit'),
            'class'          => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event'  => 'saveAndContinueEdit',
                        'target' => '#edit_form'
                    ]
                ]
            ]
        ], -100);

        $profile = $this->coreRegistry->registry('mageplaza_orderexport_profile');

        if ($profile->getId()) {
            $this->buttonList->add('generate', [
                'label'   => __('Generate'),
                'class'   => 'save',
            ], -90);

            $this->buttonList->add('delivery', [
                'label'   => __('Delivery'),
                'class'   => 'save',
                'onclick' => sprintf(
                    "location.href = '%s';",
                    $this->getUrl('*/*/delivery', ['id' => $profile->getId()])
                ),
            ], -90);
        }
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        /** @var Profile $profile */
        $profile = $this->coreRegistry->registry('mageplaza_orderexport_profile');
        if ($id = $profile->getId()) {
            return $this->getUrl('*/*/save', ['id' => $id]);
        }

        return parent::getFormActionUrl();
    }
}
