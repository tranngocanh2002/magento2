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

namespace Mageplaza\OrderExport\Model\Rule\Condition;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Directory\Model\Config\Source\Allregion;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Payment\Model\Config\Source\Allmethods as PaymentAllMethods;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Config\Source\Allmethods as ShippingAllMethods;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Attribute
 * @package Mageplaza\OrderExport\Model\Rule\Condition
 */
class Attribute extends AbstractCondition
{
    /**
     * @var Country
     */
    protected $_directoryCountry;

    /**
     * @var Allregion
     */
    protected $_directoryAllRegion;

    /**
     * @var ShippingAllMethods
     */
    protected $_shippingAllMethods;

    /**
     * @var PaymentAllMethods
     */
    protected $_paymentAllMethods;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * Attribute constructor.
     *
     * @param Context $context
     * @param Country $directoryCountry
     * @param Allregion $directoryAllRegion
     * @param ShippingAllMethods $shippingAllMethods
     * @param PaymentAllMethods $paymentAllMethods
     * @param RequestInterface $request
     * @param ProfileFactory $profileFactory
     * @param ProductRepository $productRepository
     * @param EavConfig $eavConfig
     * @param Yesno $yesno
     * @param array $data
     */
    public function __construct(
        Context $context,
        Country $directoryCountry,
        Allregion $directoryAllRegion,
        ShippingAllMethods $shippingAllMethods,
        PaymentAllMethods $paymentAllMethods,
        RequestInterface $request,
        ProfileFactory $profileFactory,
        ProductRepository $productRepository,
        EavConfig $eavConfig,
        Yesno $yesno,
        array $data = []
    ) {
        $this->_directoryCountry   = $directoryCountry;
        $this->_directoryAllRegion = $directoryAllRegion;
        $this->_shippingAllMethods = $shippingAllMethods;
        $this->_paymentAllMethods  = $paymentAllMethods;
        $this->request             = $request;
        $this->profileFactory      = $profileFactory;
        $this->productRepository   = $productRepository;
        $this->eavConfig           = $eavConfig;
        $this->yesno               = $yesno;

        parent::__construct($context, $data);
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'base_subtotal'                => __('Subtotal (Excl. Tax)'),
            'base_subtotal_incl_tax'       => __('Subtotal (Incl. Tax)'),
            'total_qty_ordered'            => __('Total Items Quantity'),
            'weight'                       => __('Total Weight'),
            'payment_method'               => __('Payment Method'),
            'shipping_method'              => __('Shipping Method'),
            'postcode'                     => __('Shipping Postcode'),
            'region'                       => __('Shipping Region'),
            'region_id'                    => __('Shipping State/Province'),
            'country_id'                   => __('Shipping Country'),
            'customer_id'                  => __('Customer ID'),
            'customer_is_guest'            => __('Customer ID Guest'),
            'customer_note_notify'         => __('Customer Note Notify'),
            'customer_dob'                 => __('Customer Dob'),
            'customer_email'               => __('Customer Email'),
            'customer_firstname'           => __('Customer First Name'),
            'customer_lastname'            => __('Customer  Last Name'),
            'customer_middlename'          => __('Customer Middle Name'),
            'customer_prefix'              => __('Customer  Prefix'),
            'customer_suffix'              => __('Customer Suffix'),
            'customer_taxvat'              => __('Customer Taxvat'),
            'ext_customer_id'              => __('Ext Customer ID'),
            'customer_note'                => __('Customer Note'),
            'customer_gender'              => __('Customer Gender'),
            'paypal_ipn_customer_notified' => __('Paypal Ipn Customer Notified')
        ];

        $profileType = $this->getProfileType();

        if (!$profileType) {
            $profileType = $this->request->getParam('type');
        }

        switch ($profileType) {
            case Profile::TYPE_SHIPMENT:
                unset($attributes['base_subtotal']);
                unset($attributes['base_subtotal_incl_tax']);
                break;
            default:
                break;
        }

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal':
            case 'weight':
            case 'total_qty':
            case 'customer_id':
            case 'customer_note_notify':
                return 'numeric';

            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
            case 'customer_gender':
            case 'customer_is_guest':
                return 'select';
        }

        return 'string';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
            case 'customer_gender':
            case 'customer_is_guest':
                return 'select';
        }
        return 'text';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = $this->_directoryCountry->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_directoryAllRegion->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = $this->_shippingAllMethods->toOptionArray();
                    break;

                case 'payment_method':
                    $options = $this->_paymentAllMethods->toOptionArray();
                    break;

                case 'customer_gender':
                    try {
                        $options = $this->eavConfig->getAttribute('customer', 'gender')
                            ->getSource()->getAllOptions();
                    } catch (Exception $e) {
                        $options = [];
                    }
                    break;

                case 'customer_is_guest':
                    $options = $this->yesno->toOptionArray();
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $profileType = $this->getProfileType($model);
        $shipping    = [
            'postcode',
            'region',
            'region_id',
            'country_id'
        ];

        if ($profileType !== Profile::TYPE_ORDER) {
            $model->setData($this->getAttribute(), $model->getOrder()->getData($this->getAttribute()));
        }

        switch ($profileType) {
            case Profile::TYPE_INVOICE:
                /** @var Invoice $model */
                $this->setPaymentAndShippingInformation($model, $shipping);
                $model->setData('total_qty_ordered', $model->getTotalQty());
                $weight = 0;
                foreach ($model->getAllItems() as $item) {
                    if (!$item->getOrderItem()->getParentItem()) {
                        try {
                            $product = $this->productRepository->getById($item->getProductId());
                            $weight += $product->getWeight();
                        } catch (Exception $e) {
                            $weight = 0;
                        }
                    }
                }
                $model->setData('weight', $weight);
                break;
            case Profile::TYPE_SHIPMENT:
                /** @var Shipment $model */
                $this->setPaymentAndShippingInformation($model, $shipping);
                $model->setData('total_qty_ordered', $model->getTotalQty());
                $model->setData('weight', $model->getTotalWeight());
                break;
            case Profile::TYPE_CREDITMEMO:
                /** @var Creditmemo $model */
                $totalQty = 0;
                $weight   = 0;
                foreach ($model->getAllItems() as $item) {
                    if (!$item->getOrderItem()->getParentItem()) {
                        $totalQty += $item->getQty();

                        try {
                            $product = $this->productRepository->getById($item->getProductId());
                            $weight += $product->getWeight();
                        } catch (Exception $e) {
                            $weight = 0;
                        }
                    }
                }
                $model->setData('total_qty_ordered', $totalQty);
                $model->setData('weight', $weight);
                $this->setPaymentAndShippingInformation($model, $shipping);
                break;
            default:
                /** @var Order $model */
                $this->setPaymentAndShippingInformation($model, $shipping, true);
                break;
        }

        return parent::validate($model);
    }

    /**
     * @param null $model
     *
     * @return Profile
     */
    public function getProfileType($model = null)
    {
        if ($model && $this->request->getFullActionName() === 'mporderexport_manageprofiles_massGenerate') {
            return $model->getData('profile_type');
        }

        $profileId = $this->request->getParam('id');

        return $this->profileFactory->create()->load($profileId)->getProfileType();
    }

    /**
     * @param AbstractModel $model
     * @param array $shippingArray
     * @param bool $isDefault
     */
    public function setPaymentAndShippingInformation($model, $shippingArray, $isDefault = false)
    {
        if ($isDefault) {
            $model->setPaymentMethod($model->getPayment()->getMethod());
            if (in_array($this->getAttribute(), $shippingArray, true)) {
                $model->setData(
                    $this->getAttribute(),
                    $model->getShippingAddress()->getData($this->getAttribute())
                );
            }
        } else {
            $model->setPaymentMethod($model->getOrder()->getPayment()->getMethod());
            $model->setShippingMethod($model->getOrder()->getShippingMethod());
            if (in_array($this->getAttribute(), $shippingArray, true)) {
                $model->setData(
                    $this->getAttribute(),
                    $model->getOrder()->getShippingAddress()->getData($this->getAttribute())
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }
}
