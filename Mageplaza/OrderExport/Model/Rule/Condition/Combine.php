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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Rule\Model\Condition\Combine as RuleCombine;
use Magento\Rule\Model\Condition\Context;
use Magento\SalesRule\Model\Rule\Condition\Combine as SalesRuleCombine;
use Mageplaza\OrderExport\Model\Rule\Condition\Product\Found;
use Mageplaza\OrderExport\Model\Rule\Condition\Product\Subselect;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Combine
 * @package Mageplaza\OrderExport\Model\Rule\Condition
 */
class Combine extends RuleCombine
{
    /**
     * @var Attribute
     */
    protected $conditionAttribute;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * Combine constructor.
     *
     * @param Context $context
     * @param Attribute $conditionAttribute
     * @param RequestInterface $request
     * @param ProfileFactory $profileFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $conditionAttribute,
        RequestInterface $request,
        ProfileFactory $profileFactory,
        array $data = []
    ) {
        $this->conditionAttribute = $conditionAttribute;
        $this->request            = $request;
        $this->profileFactory     = $profileFactory;

        parent::__construct($context, $data);
        $this->setType(SalesRuleCombine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->conditionAttribute->loadAttributeOptions()->getAttributeOption();
        $attributes        = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Mageplaza\OrderExport\Model\Rule\Condition\Attribute|' . $code,
                'label' => $label,
            ];
        }

        $profileType = $this->getProfile()->getProfileType();

        if (!$profileType) {
            $profileType = $this->request->getParam('type');
        }

        switch ($profileType) {
            case Profile::TYPE_INVOICE:
                $label = __('Invoice Attribute');
                break;
            case Profile::TYPE_SHIPMENT:
                $label = __('Shipment Attribute');
                break;
            case Profile::TYPE_CREDITMEMO:
                $label = __('Creditmemo Attribute');
                break;
            default:
                $label = __('Order Attribute');
                break;
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => Found::class,
                    'label' => __('Product attribute combination'),
                ],
                [
                    'value' => Subselect::class,
                    'label' => __('Products subselection')
                ],
                [
                    'value' => SalesRuleCombine::class,
                    'label' => __('Conditions combination')
                ],
                ['label' => $label, 'value' => $attributes],
            ]
        );

        $additional           = new DataObject();
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        $profileId = $this->request->getParam('id');

        return $this->profileFactory->create()->load($profileId);
    }
}
