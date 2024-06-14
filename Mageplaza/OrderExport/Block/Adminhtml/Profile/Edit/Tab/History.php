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

use DateTime;
use DateTimeZone;
use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\OrderExport\Block\Widget\Grid\Column\Renderer\Download;
use Mageplaza\OrderExport\Helper\Data as HelperData;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ResourceModel\History\CollectionFactory;

/**
 * Class History
 * @package Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab
 */
class History extends Extended implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * History constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param TimezoneInterface $timezone
     * @param Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param CollectionFactory $historyCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        HelperData $helperData,
        TimezoneInterface $timezone,
        Data $backendHelper,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        CollectionFactory $historyCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        $this->coreRegistry = $coreRegistry;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->helperData = $helperData;
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('history_grid');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $profile = $this->getProfile();
        $collection = $this->collectionFactory->getReport('mageplaza_orderexport_logs_listing_data_source');
        $collection->addFieldToSelect('*');
        $collection = $collection->addFieldToFilter('profile_id', $profile->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_id', [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'id',
            'type' => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('type', [
            'header' => __('Type'),
            'name' => 'type',
            'index' => 'type',
        ]);
        $this->addColumn('generate_status', [
            'header' => __('Generation status'),
            'name' => 'generate_status',
            'index' => 'generate_status',
        ]);
        $this->addColumn('delivery_status', [
            'header' => __('Delivery'),
            'name' => 'delivery_status',
            'index' => 'delivery_status',
        ]);
        $this->addColumn('file', [
            'header' => __('Download'),
            'name' => 'file',
            'index' => 'file',
            'renderer' => Download::class,
        ]);
        $this->addColumn('message', [
            'header' => __('Message'),
            'name' => 'message',
            'index' => 'message',
        ]);
        $this->addColumn('created_at', [
            'header' => __('Generation time'),
            'filter' => false,
            'index' => 'created_at',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        return $this;
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _afterLoadCollection()
    {
        foreach ($this->getCollection()->getItems() as $history) {
            $history->setData('created_at', $this->convertToLocaleTime($history->getCreatedAt()));
        }

        return parent::_afterLoadCollection(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $dataTime
     * @return string
     * @throws Exception
     */
    public function convertToLocaleTime($dataTime)
    {
        $localTime = new DateTime($dataTime, new DateTimeZone('UTC'));
        $localTime->setTimezone(new DateTimeZone($this->timezone->getConfigTimezone()));

        return $localTime->format('Y-m-d H:i:s');
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/log', ['id' => $this->getProfile()->getId()]);
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->coreRegistry->registry('mageplaza_orderexport_profile');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Logs');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mporderexport/logs/log', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
