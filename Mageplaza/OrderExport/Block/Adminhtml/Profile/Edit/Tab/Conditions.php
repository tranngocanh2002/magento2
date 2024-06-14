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

namespace Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as MagentoCondition;
use Magento\Sales\Model\Config\Source\Order\Status as OrderStatus;
use Magento\Sales\Ui\Component\Listing\Column\Creditmemo\State\Options;
use Magento\Sales\Ui\Component\Listing\Column\Invoice\State\Options as InvoiceOptions;
use Magento\Store\Model\System\Store;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class Conditions
 * @package Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab
 */
class Conditions extends Generic implements TabInterface
{
    /**
     * @var Options
     */
    protected $creditmemoStates;

    /**
     * @var OrderStatus
     */
    protected $orderStatus;

    /**
     * @var CustomerGroup
     */
    protected $customerGroup;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var InvoiceOptions
     */
    protected $invoiceStates;

    /**
     * @var Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var MagentoCondition
     */
    protected $conditions;

    /**
     * Conditions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Options $creditmemoStates
     * @param InvoiceOptions $invoiceStates
     * @param OrderStatus $orderStatus
     * @param CustomerGroup $customerGroup
     * @param Store $systemStore
     * @param Yesno $yesno
     * @param Fieldset $rendererFieldset
     * @param MagentoCondition $conditions
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Options $creditmemoStates,
        InvoiceOptions $invoiceStates,
        OrderStatus $orderStatus,
        CustomerGroup $customerGroup,
        Store $systemStore,
        Yesno $yesno,
        Fieldset $rendererFieldset,
        MagentoCondition $conditions,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->creditmemoStates = $creditmemoStates;
        $this->invoiceStates    = $invoiceStates;
        $this->orderStatus      = $orderStatus;
        $this->customerGroup    = $customerGroup;
        $this->systemStore      = $systemStore;
        $this->yesno            = $yesno;
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions       = $conditions;
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Profile $profile */
        $profile = $this->_coreRegistry->registry('mageplaza_orderexport_profile');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('profile_');
        $form->setFieldNameSuffix('profile');

        $conditionsFieldset = $form->addFieldset('attribute_conditions_fieldset', [
            'legend' => __('Conditions'),
        ]);
        $profileType        = $profile->getProfileType();
        if (!$profileType) {
            $profileType = $this->getRequest()->getParam('type', Profile::TYPE_ORDER);
        }
        switch ($profileType) {
            case Profile::TYPE_INVOICE:
                $statusLabel            = __('Invoice status');
                $statusConditionsValues = $this->invoiceStates->toOptionArray();
                break;
            case Profile::TYPE_SHIPMENT:
                $statusLabel            = false;
                $statusConditionsValues = [];
                break;
            case Profile::TYPE_CREDITMEMO:
                $statusLabel            = __('Creditmemo status');
                $statusConditionsValues = $this->creditmemoStates->toOptionArray();
                break;
            default:
                $statusLabel            = __('Order status');
                $statusConditionsValues = $this->orderStatus->toOptionArray();
                array_shift($statusConditionsValues);
        }
        if ($statusLabel) {
            $conditionsFieldset->addField('status_condition', 'multiselect', [
                'name'   => 'status_condition',
                'label'  => $statusLabel,
                'title'  => $statusLabel,
                'values' => $statusConditionsValues
            ])->setSize(5);
        }
        $conditionsFieldset->addField('customer_groups', 'multiselect', [
            'name'   => 'customer_groups',
            'label'  => __('Customer Groups'),
            'title'  => __('Customer Groups'),
            'values' => $this->customerGroup->toOptionArray()
        ])->setSize(5);
        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $conditionsFieldset->addField('store_ids', 'multiselect', [
                'name'     => 'store_ids',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);
        } else {
            $conditionsFieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }
        if ($profileType === 'order') {
            $conditionsFieldset->addField('change_stt', 'select', [
                'name'   => 'change_stt',
                'label'  => __('Change order status'),
                'title'  => __('Change order status'),
                'note'   => __('Change order status after being generated'),
                'values' => $this->orderStatus->toOptionArray()
            ]);
        }
        $dateFormat = 'MM/d/Y';
        $conditionsFieldset->addField('created_from', 'date', [
            'name'        => 'created_from',
            'label'       => __('Created From'),
            'title'       => __('Created From'),
            'date_format' => $dateFormat,
        ]);
        $conditionsFieldset->addField('created_to', 'date', [
            'name'        => 'created_to',
            'label'       => __('Created To'),
            'title'       => __('Created To'),
            'date_format' => $dateFormat,
        ]);
        $conditionsFieldset->addField('order_id_from', 'text', [
            'name'  => 'order_id_from',
            'label' => __('Order Id From'),
            'title' => __('Order Id From'),
            'class' => 'validate-not-negative-number'
        ]);
        $conditionsFieldset->addField('order_id_to', 'text', [
            'name'  => 'order_id_to',
            'label' => __('Order Id To'),
            'title' => __('Order Id To'),
            'class' => 'validate-not-negative-number'
        ]);
        if ($profileType !== Profile::TYPE_ORDER) {
            $conditionsFieldset->addField('item_id_from', 'text', [
                'name'  => 'item_id_from',
                'label' => ucfirst($profileType) . __(' Id From'),
                'title' => ucfirst($profileType) . __(' Id From'),
                'class' => 'validate-not-negative-number'
            ]);
            $conditionsFieldset->addField('item_id_to', 'text', [
                'name'  => 'item_id_to',
                'label' => ucfirst($profileType) . __(' Id To'),
                'title' => ucfirst($profileType) . __(' Id To'),
                'class' => 'validate-not-negative-number'
            ]);
        }

        $type = $profile->getProfileType();
        if (!$type) {
            $type = $this->_request->getParam('type');
        }

        $fieldset = $form->addFieldset('conditions_fieldset', [
            'legend' => __('Apply the rule only if following conditions are met(leave blank for all items)'),
        ]);

        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl($this->getUrl(
            'mporderexport/condition/newConditionHtml/form/profile_conditions_fieldset',
            ['profile_type' => $type]
        ));

        $fieldset->setRenderer($renderer);

        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions')]
        )->setRule($profile)->setRenderer($this->conditions);

        $conditionsFieldset->addField('export_duplicate', 'select', [
            'name'               => 'export_duplicate',
            'label'              => __('Export Duplicate'),
            'title'              => __('Export Duplicate'),
            'values'             => $this->yesno->toOptionArray(),
            'value'              => '0',
            'after_element_html' => '<a style="width: 15%;" id="reset-flag" class="btn primary">' . __('Reset Flag') . '</a>',
            'note'               => __('If chose "No", the item which has already been exported will not be exported again.')
        ]);

        $form->addValues($profile->getData());
        $profile->getConditions()->setJsFormObject('profile_conditions_fieldset');
        $this->setConditionFormName($profile->getConditions(), 'profile_conditions_fieldset');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get form Html
     *
     * @return string
     */
    public function getFormHtml()
    {
        $formHtml  = parent::getFormHtml();
        $childHtml = $this->getChildHtml();

        return $formHtml . $childHtml;
    }
}
