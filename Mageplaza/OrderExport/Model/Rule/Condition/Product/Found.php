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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\Rule\Condition\Combine as ConditionCombine;
use Mageplaza\OrderExport\Model\Rule\Condition\Product;
use Mageplaza\OrderExport\Model\Rule\Condition\Product\Combine as ProductCombine;

/**
 * Class Found
 * @package Mageplaza\OrderExport\Model\Rule\Condition\Product
 */
class Found extends ProductCombine
{
    /**
     * Found constructor.
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
        $this->setType(Found::class);
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption([1 => __('FOUND'), 0 => __('NOT FOUND')]);
        return $this;
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
            "If an item is %1 in the %2 with %3 of these conditions true:",
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
     * Validate
     *
     * @param AbstractModel $model
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate(AbstractModel $model)
    {
        $all   = $this->getAggregator() === 'all';
        $true  = (bool)$this->getValue();
        $found = false;

        foreach ($model->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if ($all && !$validated || !$all && $validated) {
                    $found = $validated;
                    break;
                }
            }
            if ($found && $true || !$true && $found) {
                break;
            }
        }
        if ($found && $true) {
            return true;
        } elseif (!$found && !$true) {
            return true;
        }

        return false;
    }
}
