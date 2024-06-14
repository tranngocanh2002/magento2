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

use Magento\Backend\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ResourceModelProduct;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Product\AbstractProduct;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\Rule\Condition\Combine as ConditionCombine;

/**
 * Class Product
 * @package Mageplaza\OrderExport\Model\Rule\Condition
 */
class Product extends AbstractProduct
{
    /**
     * @var Combine
     */
    protected $combineCondition;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param Data $backendData
     * @param Config $config
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ResourceModelProduct $productResource
     * @param Collection $attrSetCollection
     * @param FormatInterface $localeFormat
     * @param Combine $combineCondition
     * @param RequestInterface $request
     * @param array $data
     * @param ProductCategoryList|null $categoryList
     */
    public function __construct(
        Context $context,
        Data $backendData,
        Config $config,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        ResourceModelProduct $productResource,
        Collection $attrSetCollection,
        FormatInterface $localeFormat,
        ConditionCombine $combineCondition,
        RequestInterface $request,
        array $data = [],
        ProductCategoryList $categoryList = null
    ) {
        $this->combineCondition = $combineCondition;
        $this->request          = $request;

        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data,
            $categoryList
        );
    }

    /**
     * Add special attributes
     *
     * @param array $attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);

        switch ($this->getProfileType()) {
            case Profile::TYPE_INVOICE:
                $labelQty   = __('Quantity in invoice');
                $labelPrice = __('Price in invoice');
                $labelTotal = __('Row total in invoice');
                break;
            case Profile::TYPE_CREDITMEMO:
                $labelQty   = __('Quantity in creditmemo');
                $labelPrice = __('Price in creditmemo');
                $labelTotal = __('Row total in creditmemo');
                break;
            case Profile::TYPE_SHIPMENT:
                $labelQty   = __('Quantity in shipment');
                $labelPrice = __('Price in shipment');
                $labelTotal = __('Row total in shipment');
                break;
            default:
                $labelQty   = __('Quantity in order');
                $labelPrice = __('Price in order');
                $labelTotal = __('Row total in order');
                break;
        }

        $attributes['quote_item_qty']       = $labelQty;
        $attributes['quote_item_price']     = $labelPrice;
        $attributes['quote_item_row_total'] = $labelTotal;

        $attributes['parent::category_ids']   = __('Category (Parent only)');
        $attributes['children::category_ids'] = __('Category (Children Only)');
    }

    /**
     * Retrieve attribute
     *
     * @return string
     */
    public function getAttribute(): string
    {
        $attribute = $this->getData('attribute');
        if (strpos($attribute, '::') !== false) {
            [, $attribute] = explode('::', $attribute);
        }

        return $attribute;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeName()
    {
        $attribute = $this->getAttribute();
        if ($this->getAttributeScope()) {
            $attribute = $this->getAttributeScope() . '::' . $attribute;
        }

        return $this->getAttributeOption($attribute);
    }

    /**
     * @inheritdoc
     */
    public function loadAttributeOptions()
    {
        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        foreach ($productAttributes as $attribute) {
            /* @var EavAttribute $attribute */
            if (!$attribute->isAllowedForRuleCondition()
                || !$attribute->getDataUsingMethod($this->_isUsedForRuleProperty)
            ) {
                continue;
            }
            $frontLabel = $attribute->getFrontendLabel();
            $attributes[$attribute->getAttributeCode()] = $frontLabel;
            $attributes['parent::' . $attribute->getAttributeCode()] = $frontLabel . __('(Parent Only)');
            $attributes['children::' . $attribute->getAttributeCode()] = $frontLabel . __('(Children Only)');
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeElementHtml()
    {
        $html = parent::getAttributeElementHtml() .
                $this->getAttributeScopeElement()->getHtml();

        return $html;
    }

    /**
     * Retrieve form element for scope element
     *
     * @return AbstractElement
     */
    private function getAttributeScopeElement(): AbstractElement
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__attribute_scope',
            'hidden',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][attribute_scope]',
                'value' => $this->getAttributeScope(),
                'no_span' => true,
                'class' => 'hidden',
                'data-form-part' => $this->getFormName(),
            ]
        );
    }

    /**
     * Set attribute value
     *
     * @param string $value
     * @return void
     */
    public function setAttribute(string $value)
    {
        if (strpos($value, '::') !== false) {
            [$scope, $attribute] = explode('::', $value);
            $this->setData('attribute_scope', $scope);
            $this->setData('attribute', $attribute);
        } else {
            $this->setData('attribute', $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function loadArray($arr)
    {
        parent::loadArray($arr);
        $this->setAttributeScope($arr['attribute_scope'] ?? null);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function asArray(array $arrAttributes = [])
    {
        $out = parent::asArray($arrAttributes);
        $out['attribute_scope'] = $this->getAttributeScope();

        return $out;
    }

    /**
     * Validate Product Rule Condition
     *
     * @param AbstractModel $model
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function validate(AbstractModel $model)
    {
        /** @var ModelProduct $product */
        $product = $model->getProduct();
        if (!$product instanceof ModelProduct) {
            $product = $this->productRepository->getById($model->getProductId());
        }

        $quoteItemPrice = $model->getPrice();

        switch ($this->getProfileType()) {
            case Profile::TYPE_INVOICE:
            case Profile::TYPE_CREDITMEMO:
                $quoteItemQty      = $model->getQty();
                $quoteItemRowTotal = $model->getBaseRowTotal();
                break;
            case Profile::TYPE_SHIPMENT:
                $quoteItemQty      = $model->getQty();
                $quoteItemRowTotal = $model->getRowTotal();
                break;
            default:
                $quoteItemQty      = $model->getQtyOrdered();
                $quoteItemRowTotal = $model->getBaseRowTotal();
                break;
        }

        $product->setQuoteItemQty(
            $quoteItemQty
        )->setQuoteItemPrice(
            $quoteItemPrice
        )->setQuoteItemRowTotal(
            $quoteItemRowTotal
        );

        $attrCode = $this->getAttribute();

        if ($attrCode === 'category_ids') {
            return $this->validateAttribute($this->_getAvailableInCategories($product->getId()));
        }

        if ($attrCode === 'quote_item_price') {
            $numericOperations = $this->getDefaultOperatorInputByType()['numeric'];
            if (in_array($this->getOperator(), $numericOperations)) {
                $this->setData('value', $this->getFormattedPrice($this->getValue()));
            }
        }

        return parent::validate($product);
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'sales_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                }
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }

    /**
     * Get locale-based formatted price.
     *
     * @param string $value
     * @return float|null
     */
    private function getFormattedPrice($value)
    {
        $value = preg_replace('/[^0-9^\^.,-]/m', '', $value);

        /**
         * If the comma is the third symbol in the number, we consider it to be a decimal separator
         */
        $separatorComa = strpos($value, ',');
        $separatorDot = strpos($value, '.');
        if ($separatorComa !== false && $separatorDot === false && preg_match('/,\d{3}$/m', $value) === 1) {
            $value .= '.00';
        }
        return $this->_localeFormat->getNumber($value);
    }

    /**
     * @return mixed
     */
    public function getProfileType()
    {
        $profile = $this->combineCondition->getProfile();
        $type    = $profile->getProfileType();

        if ($this->request->getFullActionName() === 'mporderexport_condition_newConditionHtml') {
            $type = $this->request->getParam('profile_type');
        }

        return $type;
    }
}
