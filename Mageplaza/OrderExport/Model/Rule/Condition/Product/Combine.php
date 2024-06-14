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
use Magento\Rule\Model\Condition\Combine as RuleCombine;
use Magento\Rule\Model\Condition\Context;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\Rule\Condition\Combine as ConditionCombine;
use Mageplaza\OrderExport\Model\Rule\Condition\Product;

/**
 * Class Combine
 * @package Mageplaza\OrderExport\Model\Rule\Condition\Product
 */
class Combine extends RuleCombine
{
    /**
     * @var Product
     */
    protected $_ruleConditionProd;

    /**
     * @var ConditionCombine
     */
    protected $combineCondition;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Combine constructor.
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
        $this->_ruleConditionProd = $ruleConditionProduct;
        $this->combineCondition   = $combineCondition;
        $this->request            = $request;

        parent::__construct($context, $data);
        $this->setType(Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $pAttributes       = [];
        $iAttributes       = [];
        $productAttributes = $this->_ruleConditionProd->loadAttributeOptions()->getAttributeOption();

        switch ($this->getProfileType()) {
            case Profile::TYPE_INVOICE:
                $labelAttribute = __('Invoice Item Attribute');
                break;
            case Profile::TYPE_CREDITMEMO:
                $labelAttribute = __('Creditmemo Item Attribute');
                break;
            case Profile::TYPE_SHIPMENT:
                $labelAttribute = __('Shipment Item Attribute');
                break;
            default:
                $labelAttribute = __('Order Item Attribute');
                break;
        }

        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = [
                    'value' => Product::class . '|' . $code,
                    'label' => $label,
                ];
            } else {
                $pAttributes[] = [
                    'value' => Product::class . '|' . $code,
                    'label' => $label,
                ];
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => Combine::class,
                    'label' => __('Conditions Combination'),
                ],
                ['label' => $labelAttribute, 'value' => $iAttributes],
                ['label' => __('Product Attribute'), 'value' => $pAttributes]
            ]
        );

        return $conditions;
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
