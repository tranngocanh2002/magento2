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

namespace Mageplaza\OrderExport\Model\Config\Source;

use Mageplaza\OrderExport\Model\Config\AbstractSource;
use Mageplaza\OrderExport\Model\DefaultTemplateFactory;

/**
 * Class DefaultTemplate
 * @package Mageplaza\OrderExport\Model\Config\Source
 */
class DefaultTemplate extends AbstractSource
{
    /**
     * @var DefaultTemplateFactory
     */
    private $defaultTemplateFactory;

    /**
     * DefaultTemplate constructor.
     *
     * @param DefaultTemplateFactory $defaultTemplateFactory
     */
    public function __construct(DefaultTemplateFactory $defaultTemplateFactory)
    {
        $this->defaultTemplateFactory = $defaultTemplateFactory;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $templateCollection = $this->defaultTemplateFactory->create()->getCollection();
        $array = [];
        foreach ($templateCollection as $template) {
            $array[$template->getName()] = $template->getTitle();
        }

        return $array;
    }

    /**
     * @return array
     */
    public function toArrayWithType()
    {
        $templateCollection = $this->defaultTemplateFactory->create()->getCollection();
        $array = [];
        foreach ($templateCollection as $template) {
            $array[$template->getName()] = [
                'type' => $template->getFileType(),
                'profileType' => $template->getProfileType(),
                'label' => $template->getTitle()
            ];
        }

        return $array;
    }
}
