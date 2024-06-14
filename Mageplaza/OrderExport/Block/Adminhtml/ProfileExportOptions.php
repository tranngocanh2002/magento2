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

namespace Mageplaza\OrderExport\Block\Adminhtml;

use Magento\Config\Model\Config\Source\Enabledisable;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class ProfileExportOptions
 * @package Mageplaza\OrderExport\Block\Adminhtml
 */
class ProfileExportOptions
{
    const TYPE = '';

    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var array
     */
    public $cvs;

    /**
     * @var array
     */
    public $xml;

    /**
     * ProfileExportOptions constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Data $helperData
     */
    public function __construct(ProfileFactory $profileFactory, Data $helperData)
    {
        $this->cvs = [
            'url' => 'mui/export/gridToCsv',
            'value' => 'csv',
            'label' => __('CSV')
        ];
        $this->xml = [
            'url' => 'mui/export/gridToXml',
            'value' => 'xml',
            'label' => __('Excel XML')
        ];

        if ($helperData->isEnabled()) {
            $this->profileFactory = $profileFactory;
            $collection = $this->profileFactory->create()->getCollection()
                ->addFieldToFilter('profile_type', $this::TYPE)
                ->addFieldToFilter('status', Enabledisable::ENABLE_VALUE);

            foreach ($collection as $profile) {
                $this->{'profile_' . $profile->getId()} = [
                    'url' => 'mporderexport/manageprofiles/quickexport/id/' . $profile->getId(),
                    'value' => $profile->getId(),
                    'label' => __('Profile %1', $profile->getName())
                ];
            }
        }
    }
}
