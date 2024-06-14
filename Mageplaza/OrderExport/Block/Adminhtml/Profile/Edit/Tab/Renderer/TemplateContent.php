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

namespace Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab\Renderer;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Address as AddressResource;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo as CreditmemoResource;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResource;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\ResourceModel\Order\Item as OrderItem;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Item as ShipmentItem;
use Mageplaza\OrderExport\Block\Adminhtml\LiquidFilters;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Config\Source\DefaultTemplate;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class TemplateContent
 * @package Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab\Renderer
 */
class TemplateContent extends Element implements RendererInterface
{
    /**
     * @var array
     */
    public $generalAttr = [
        'adjustment_negative',
        'adjustment_positive',
        'applied_rule_ids',
        'base_adjustment_negative',
        'base_adjustment_positive',
        'base_currency_code',
        'base_to_global_rate',
        'base_to_order_rate',
        'billing_address_id',
        'can_ship_partially',
        'can_ship_partially_item',
        'coupon_code',
        'coupon_rule_name',
        'created_at',
        'edit_increment',
        'email_sent',
        'entity_id',
        'ext_customer_id',
        'ext_order_id',
        'forced_shipment_with_invoice',
        'gift_message_id',
        'global_currency_code',
        'hold_before_state',
        'hold_before_status',
        'increment_id',
        'is_virtual',
        'order_currency_code',
        'original_increment_id',
        'payment_auth_expiration',
        'payment_authorization_amount',
        'paypal_ipn_customer_notified',
        'protect_code',
        'quote_address_id',
        'quote_id',
        'relation_child_id',
        'relation_child_real_id',
        'relation_parent_id',
        'relation_parent_real_id',
        'remote_ip',
        'send_email',
        'state',
        'status',
        'updated_at',
        'weight',
        'x_forwarded_for'
    ];

    /**
     * @var array
     */
    public $customerAttr = [
        'customer_dob',
        'customer_email',
        'customer_firstname',
        'customer_gender',
        'customer_group_id',
        'customer_id',
        'customer_is_guest',
        'customer_lastname',
        'customer_middlename',
        'customer_note',
        'customer_note_notify',
        'customer_prefix',
        'customer_suffix',
        'customer_taxvat',
    ];

    /**
     * @var array
     */
    public $shippingAttr = [
        'shipping_address_id',
        'shipping_amount',
        'shipping_canceled',
        'shipping_description',
        'shipping_discount_amount',
        'shipping_discount_tax_compensation_amount',
        'shipping_incl_tax',
        'shipping_invoiced',
        'shipping_method',
        'shipping_refunded',
        'shipping_tax_amount',
        'shipping_tax_refunded',
        'base_shipping_amount',
        'base_shipping_canceled',
        'base_shipping_discount_amount',
        'base_shipping_discount_tax_compensation_amnt',
        'base_shipping_incl_tax',
        'base_shipping_invoiced',
        'base_shipping_refunded',
        'base_shipping_tax_amount',
        'base_shipping_tax_refunded',
    ];

    /**
     * @var array
     */
    public $discountAttr = [
        'base_discount_amount',
        'base_discount_canceled',
        'base_discount_invoiced',
        'base_discount_refunded',
        'base_discount_tax_compensation_amount',
        'base_discount_tax_compensation_invoiced',
        'base_discount_tax_compensation_refunded',
        'discount_amount',
        'discount_canceled',
        'discount_description',
        'discount_invoiced',
        'discount_refunded',
        'discount_tax_compensation_amount',
        'discount_tax_compensation_invoiced',
        'discount_tax_compensation_refunded',
    ];

    /**
     * @var array
     */
    public $totalAttr = [
        'grand_total',
        'base_grand_total',
        'base_total_canceled',
        'base_total_due',
        'base_total_invoiced',
        'base_total_invoiced_cost',
        'base_total_offline_refunded',
        'base_total_online_refunded',
        'base_total_paid',
        'base_total_qty_ordered',
        'base_total_refunded',
        'total_canceled',
        'total_due',
        'total_invoiced',
        'total_item_count',
        'total_offline_refunded',
        'total_online_refunded',
        'total_paid',
        'total_qty_ordered',
        'total_refunded',
        'subtotal',
        'base_subtotal',
        'base_subtotal_canceled',
        'base_subtotal_incl_tax',
        'base_subtotal_invoiced',
        'base_subtotal_refunded',
        'subtotal_canceled',
        'subtotal_incl_tax',
        'subtotal_invoiced',
        'subtotal_refunded',

    ];

    /**
     * @var array
     */
    public $taxAttr = [
        'tax_amount',
        'tax_canceled',
        'tax_invoiced',
        'tax_refunded',
        'base_tax_amount',
        'base_tax_canceled',
        'base_tax_invoiced',
        'base_tax_refunded',
    ];

    /**
     * @var array
     */
    public $storeAttr = [
        'store_currency_code',
        'store_id',
        'store_name',
        'store_to_base_rate',
        'store_to_order_rate',
    ];

    /**
     * @var array
     */
    public $otherAttr = [];

    /**
     * @var string $_template
     */
    protected $_template = 'Mageplaza_OrderExport::profile/template/template_content.phtml';

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderItem
     */
    protected $orderItemResource;

    /**
     * @var InvoiceItem
     */
    protected $invoiceItemResource;

    /**
     * @var ShipmentItem
     */
    protected $shipmentItemResource;

    /**
     * @var CreditmemoItem
     */
    protected $creditmemoItemResource;

    /**
     * @var LiquidFilters
     */
    protected $liquidFilters;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var JsonHelper
     */
    protected $invoiceResource;

    /**
     * @var ShipmentResource
     */
    protected $shipmentResource;

    /**
     * @var CreditmemoResource
     */
    protected $creditmemoResource;

    /**
     * @var AddressResource
     */
    protected $addressResource;

    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var DefaultTemplate
     */
    protected $defaultTemplate;

    /**
     * TemplateContent constructor.
     *
     * @param Context $context
     * @param AddressResource $addressResource
     * @param OrderFactory $orderFactory
     * @param OrderItem $orderItemResource
     * @param InvoiceItem $invoiceItemResource
     * @param ShipmentItem $shipmentItemResource
     * @param CreditmemoItem $creditmemoItemResource
     * @param InvoiceResource $invoiceResource
     * @param ShipmentResource $shipmentResource
     * @param CreditmemoResource $creditmemoResource
     * @param Registry $registry
     * @param LiquidFilters $liquidFilters
     * @param ProfileFactory $profileFactory
     * @param DefaultTemplate $defaultTemplate
     * @param array $data
     */
    public function __construct(
        Context $context,
        AddressResource $addressResource,
        OrderFactory $orderFactory,
        OrderItem $orderItemResource,
        InvoiceItem $invoiceItemResource,
        ShipmentItem $shipmentItemResource,
        CreditmemoItem $creditmemoItemResource,
        InvoiceResource $invoiceResource,
        ShipmentResource $shipmentResource,
        CreditmemoResource $creditmemoResource,
        Registry $registry,
        LiquidFilters $liquidFilters,
        ProfileFactory $profileFactory,
        DefaultTemplate $defaultTemplate,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry               = $registry;
        $this->liquidFilters          = $liquidFilters;
        $this->orderFactory           = $orderFactory;
        $this->orderItemResource      = $orderItemResource;
        $this->invoiceItemResource    = $invoiceItemResource;
        $this->shipmentItemResource   = $shipmentItemResource;
        $this->creditmemoItemResource = $creditmemoItemResource;
        $this->invoiceResource        = $invoiceResource;
        $this->shipmentResource       = $shipmentResource;
        $this->creditmemoResource     = $creditmemoResource;
        $this->addressResource        = $addressResource;
        $this->profileFactory         = $profileFactory;
        $this->defaultTemplate        = $defaultTemplate;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        $html           = $this->toHtml();

        return $html;
    }

    /**
     * @return null
     */
    public function getFieldsList()
    {
        /** @var Profile $profile */
        $profile    = $this->registry->registry('mageplaza_orderexport_profile');
        $fieldsList = $profile->getFieldsList();
        if (!$fieldsList) {
            return null;
        }

        return $fieldsList;
    }

    /**
     * @return array
     */

    public function getProfileType()
    {
        $type = $this->_request->getParam('type');
        if (!$type) {
            $profileId = $this->getRequest()->getParam('id');
            $profile   = $this->profileFactory->create()->load($profileId);
            $type      = $profile->getProfileType();
        }
        if (!$type) {
            $type = 'order';
        }

        return $type;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getProfileAttrCollection()
    {
        $profileType = $this->getProfileType();
        switch ($profileType) {
            case Profile::TYPE_INVOICE:
                $collectionData     = $this->invoiceResource->getConnection()
                    ->describeTable($this->invoiceResource->getMainTable());
                $name               = __('Invoice');
                $itemCollectionData = $this->invoiceItemResource->getConnection()
                    ->describeTable($this->invoiceItemResource->getMainTable());
                break;
            case Profile::TYPE_SHIPMENT:
                $collectionData     = $this->shipmentResource->getConnection()
                    ->describeTable($this->shipmentResource->getMainTable());
                $name               = __('Shipment');
                $itemCollectionData = $this->shipmentItemResource->getConnection()
                    ->describeTable($this->shipmentItemResource->getMainTable());
                break;
            case Profile::TYPE_CREDITMEMO:
                $collectionData     = $this->creditmemoResource->getConnection()
                    ->describeTable($this->creditmemoResource->getMainTable());
                $name               = __('Creditmemo');
                $itemCollectionData = $this->creditmemoItemResource->getConnection()
                    ->describeTable($this->creditmemoItemResource->getMainTable());
                break;
            default:
                $collectionData     = $this->orderFactory->create()->getResource()->getConnection()
                    ->describeTable($this->orderFactory->create()->getResource()->getMainTable());
                $name               = __('Order');
                $itemCollectionData = $this->orderItemResource->getConnection()
                    ->describeTable($this->orderItemResource->getMainTable());
                break;
        }

        $attrCollection = [
            'order_general'   => [
                'label'  => __('%1 General Attributes', $name),
                'values' => [],
                'type'   => $profileType
            ],
            'order_customer'  => [
                'label'  => __('%1 Customer Attributes', $name),
                'values' => [],
                'type'   => $profileType
            ],
            'order_shipping'  => [
                'label'  => __('%1 Shipping Attributes', $name),
                'values' => [],
                'type'   => $profileType
            ],
            'order_discount'  => [
                'label'  => __('%1 Discount Attributes', $name),
                'values' => [],
                'type'   => $profileType
            ],
            'order_total'     => ['label' => __('%1 Total Attributes', $name), 'values' => [], 'type' => $profileType],
            'order_store'     => ['label' => __('Store Attributes'), 'values' => [], 'type' => $profileType],
            'order_tax'       => ['label' => __('%1 Tax Attributes', $name), 'values' => [], 'type' => $profileType],
            'other'           => ['label' => __('Other %1 Attributes', $name), 'values' => [], 'type' => $profileType],
            'item'            => ['label' => __('Item Attributes'), 'values' => [], 'type' => 'item'],
            'shippingAddress' => ['label' => __('Shipping Address Attributes'), 'values' => [], 'type' => $profileType],
            'billingAddress'  => ['label' => __('Billing Address Attributes'), 'values' => [], 'type' => $profileType],
        ];

        foreach ($itemCollectionData as $itemData) {
            $attrCollection['item']['values'][] = $itemData;
        }

        $addressCollectionData = $this->addressResource->getConnection()->describeTable(
            $this->addressResource->getMainTable()
        );

        foreach ($addressCollectionData as $addressItem) {
            $temp                                          = $addressItem;
            $temp['COLUMN_NAME']                           = 'shippingAddress.' . $addressItem['COLUMN_NAME'];
            $attrCollection['shippingAddress']['values'][] = $temp;
            $temp['COLUMN_NAME']                           = 'billingAddress.' . $addressItem['COLUMN_NAME'];
            $attrCollection['billingAddress']['values'][]  = $temp;
        }
        /** @var Attribute $item */
        foreach ($collectionData as $key => $item) {
            switch (true) {
                case in_array($key, $this->generalAttr):
                    $attrCollection['order_general']['values'][] = $item;
                    break;
                case in_array($key, $this->customerAttr):
                    $attrCollection['order_customer']['values'][] = $item;
                    break;
                case in_array($key, $this->shippingAttr):
                    $attrCollection['order_shipping']['values'][] = $item;
                    break;
                case in_array($key, $this->discountAttr):
                    $attrCollection['order_discount']['values'][] = $item;
                    break;
                case in_array($key, $this->totalAttr):
                    $attrCollection['order_total']['values'][] = $item;
                    break;
                case in_array($key, $this->storeAttr):
                    $attrCollection['order_store']['values'][] = $item;
                    break;
                case in_array($key, $this->taxAttr):
                    $attrCollection['order_tax']['values'][] = $item;
                    break;
                default:
                    $attrCollection['other']['values'][] = $item;
                    break;
            }
        }

        $attrCollection['order_customer']['values'][]  = ['COLUMN_NAME' => 'customer_group'];
        $attrCollection['order_general']['values'][]   = ['COLUMN_NAME' => 'payment_method'];
        $attrCollection['item']['values'][]            = ['COLUMN_NAME' => 'status'];
        $attrCollection['item']['values'][]            = ['COLUMN_NAME' => 'attributes'];
        $attrCollection['item']['values'][]            = ['COLUMN_NAME' => 'customizable_options'];
        $attrCollection['shippingAddress']['values'][] = ['COLUMN_NAME' => 'shippingAddress.country'];
        $attrCollection['shippingAddress']['values'][] = ['COLUMN_NAME' => 'shippingAddress.street_0'];
        $attrCollection['shippingAddress']['values'][] = ['COLUMN_NAME' => 'shippingAddress.street_1'];
        $attrCollection['shippingAddress']['values'][] = ['COLUMN_NAME' => 'shippingAddress.street_2'];
        $attrCollection['billingAddress']['values'][]  = ['COLUMN_NAME' => 'billingAddress.country'];
        $attrCollection['billingAddress']['values'][]  = ['COLUMN_NAME' => 'billingAddress.street_0'];
        $attrCollection['billingAddress']['values'][]  = ['COLUMN_NAME' => 'billingAddress.street_1'];
        $attrCollection['billingAddress']['values'][]  = ['COLUMN_NAME' => 'billingAddress.street_2'];

        if ($profileType !== Profile::TYPE_ORDER) {
            $attrCollection['order_general']['values'][]  = ['COLUMN_NAME' => 'payment_method'];
            $attrCollection['order_general']['values'][]  = ['COLUMN_NAME' => 'order_date'];
            $attrCollection['order_general']['values'][]  = ['COLUMN_NAME' => 'shipping_description'];
            $attrCollection['order_store']['values'][]    = ['COLUMN_NAME' => 'store_name'];
            $attrCollection['order_customer']['values'][] = ['COLUMN_NAME' => 'customer_firstname'];
            $attrCollection['order_customer']['values'][] = ['COLUMN_NAME' => 'customer_lastname'];
            $attrCollection['order_customer']['values'][] = ['COLUMN_NAME' => 'customer_email'];
        }
        switch ($profileType) {
            case Profile::TYPE_INVOICE:
                $attrCollection['order_general']['values'][] = ['COLUMN_NAME' => 'state_name'];
                break;
            case Profile::TYPE_SHIPMENT:
                $attrCollection['order_general']['values'][] = ['COLUMN_NAME' => 'order_status'];
                break;
            case Profile::TYPE_CREDITMEMO:
                $attrCollection['order_general']['values'][] = ['COLUMN_NAME' => 'order_status'];
                $attrCollection['order_general']['values'][] = ['COLUMN_NAME' => 'state_name'];
                break;
            default:
        }

        return $attrCollection;
    }

    /**
     * @return array
     */
    public function getModifier()
    {
        return $this->liquidFilters->getFilters();
    }

    /**
     * @return string
     */
    public function getDefaultTemplate()
    {
        return Data::jsonEncode($this->defaultTemplate->toArrayWithType());
    }
}
