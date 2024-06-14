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

namespace Mageplaza\OrderExport\Model\Rule\Condition\Product;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\Rule\Condition\Product;
use Mageplaza\OrderExport\Model\Rule\Condition\Product\Combine as ProductCombine;
use Mageplaza\OrderExport\Model\Rule\Condition\Combine as ConditionCombine;

/**
 * Class Subselect
 * @package Mageplaza\OrderExport\Model\Rule\Condition\Product
 */
class Subselect extends ProductCombine
{
    /**
     * @var ConditionCombine
     */
    protected $combineCondition;

    /**
     * Subselect constructor.
     *
     * @param Context $context
     * @param Product $ruleConditionProduct
     * @param ConditionCombine $combineCondition
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        Product $ruleConditionProduct,
        ConditionCombine $combineCondition,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $ruleConditionProduct, $combineCondition, $request, $data);
        $this->setType(Subselect::class)->setValue(null);
    }

    /**
     * Load array
     *
     * @param array $arr
     * @param string $key
     *
     * @return $this|Subselect
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);

        return $this;
    }

    /**
     * Return as xml
     *
     * @param string $containerKey
     * @param string $itemKey
     *
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = '<attribute>' .
            $this->getAttribute() .
            '</attribute>' .
            '<operator>' .
            $this->getOperator() .
            '</operator>' .
            parent::asXml(
                $containerKey,
                $itemKey
            );

        return $xml;
    }

    /**
     * Load attribute options
     *
     * @return $this|Subselect
     */
    public function loadAttributeOptions()
    {
        switch ($this->getProfileType()) {
            case Profile::TYPE_INVOICE:
            case Profile::TYPE_CREDITMEMO:
                $data = [
                    'qty'            => __('total quantity'),
                    'base_row_total' => __('total amount')
                ];
                break;
            case Profile::TYPE_SHIPMENT:
                $data = [
                    'qty'   => __('total quantity'),
                    'price' => __('total amount')
                ];
                break;
            default:
                $data = [
                    'qty_ordered'    => __('total quantity'),
                    'base_row_total' => __('total amount')
                ];
                break;
        }

        $this->setAttributeOption($data);

        return $this;
    }

    /**
     * Load value options
     *
     * @return $this|Subselect
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this|Subselect
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '=='  => __('is'),
                '!='  => __('is not'),
                '>='  => __('equals or greater than'),
                '<='  => __('equals or less than'),
                '>'   => __('greater than'),
                '<'   => __('less than'),
                '()'  => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );

        return $this;
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        switch ($this->getProfileType()) {
            case Profile::TYPE_INVOICE:
                $label = __('invoice');
                break;
            case Profile::TYPE_SHIPMENT:
                $label = __('shipment');
                break;
            case Profile::TYPE_CREDITMEMO:
                $label = __('creditmemo');
                break;
            default:
                $label = __('order');
                break;
        }

        $html = $this->getTypeElement()->getHtml() . __(
            "If %1 %2 %3 for a subselection of items in %4 matching %5 of these conditions:",
            $this->getAttributeElement()->getHtml(),
            $this->getOperatorElement()->getHtml(),
            $this->getValueElement()->getHtml(),
            $label,
            $this->getAggregatorElement()->getHtml()
        );
        if ($this->getId() !== '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        if (!$this->getConditions()) {
            return false;
        }
        $total = 0;
        $attr  = $this->getAttribute();

        foreach ($model->getAllItems() as $item) {
            $hasValidChild     = false;
            $useChildrenTotal  = ($item->getProductType() === Type::TYPE_BUNDLE);
            $childrenAttrTotal = 0;
            $children          = $item->getChildren();
            if (!empty($children)) {
                foreach ($children as $child) {
                    if (parent::validate($child)) {
                        $hasValidChild = true;
                        if ($useChildrenTotal) {
                            $childrenAttrTotal += $child->getData($attr);
                        }
                    }
                }
            }
            if ($hasValidChild || parent::validate($item)) {
                $total += ($hasValidChild && $useChildrenTotal)
                    ? $childrenAttrTotal * ($this->getProfileType() === Profile::TYPE_ORDER ?
                        $item->getQtyOrdered() : $item->getQty())
                    : $item->getData($attr);
            }
        }

        return $this->validateAttribute($total);
    }
}
