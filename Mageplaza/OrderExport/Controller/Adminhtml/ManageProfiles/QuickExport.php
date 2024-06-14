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

namespace Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\HistoryFactory;
use Mageplaza\OrderExport\Model\ProfileFactory;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class QuickExport
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class QuickExport extends AbstractManageProfiles
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var CreditmemoCollectionFactory;
     */
    protected $creditmemoFactory;

    /**
     * @var ShipmentCollectionFactory;
     */
    protected $shipmentFactory;

    /**
     * @var InvoiceCollectionFactory;
     */
    protected $invoiceFactory;

    /**
     * @var FileFactory
     */

    protected $fileFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $orderFactory;

    /**
     * QuickExport constructor.
     *
     * @param ProfileFactory $profileFactory
     * @param Registry $coreRegistry
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param HistoryFactory $historyFactory
     * @param Data $helperData
     * @param Filter $filter
     * @param CollectionFactory $orderFactory
     * @param InvoiceCollectionFactory $invoiceFactory
     * @param CreditmemoCollectionFactory $creditmemoFactory
     * @param ShipmentCollectionFactory $shipmentFactory
     */
    public function __construct(
        ProfileFactory $profileFactory,
        Registry $coreRegistry,
        Context $context,
        FileFactory $fileFactory,
        HistoryFactory $historyFactory,
        Data $helperData,
        Filter $filter,
        CollectionFactory $orderFactory,
        InvoiceCollectionFactory $invoiceFactory,
        CreditmemoCollectionFactory $creditmemoFactory,
        ShipmentCollectionFactory $shipmentFactory
    ) {
        $this->invoiceFactory    = $invoiceFactory;
        $this->fileFactory       = $fileFactory;
        $this->historyFactory    = $historyFactory;
        $this->helperData        = $helperData;
        $this->filter            = $filter;
        $this->orderFactory      = $orderFactory;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->shipmentFactory   = $shipmentFactory;

        parent::__construct($profileFactory, $coreRegistry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $selected  = $this->getRequest()->getParam('selected') ?: [];
        $component = $this->filter->getComponent();

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();

        $dataProvider = $component->getContext()->getDataProvider();
        $profile      = $this->initProfile();
        if ($selected !== 'false') {
            switch ($profile->getProfileType()) {
                case Profile::TYPE_INVOICE:
                    $collection = $this->filter->getCollection($this->invoiceFactory->create());
                    break;
                case Profile::TYPE_SHIPMENT:
                    $collection = $this->filter->getCollection($this->shipmentFactory->create());
                    break;
                case Profile::TYPE_CREDITMEMO:
                    $collection = $this->filter->getCollection($this->creditmemoFactory->create());
                    break;
                default:
                    $collection = $this->filter->getCollection($this->orderFactory->create());
            }
            $selected = $collection->getAllIds();
        } else {
            $selected = [];
            $items    = $dataProvider->getSearchResult()->getItems();
            foreach ($items as $key => $item) {
                $selected[$key] = (int) $item->getId();
            }
        }

        $resultRedirect  = $this->resultRedirectFactory->create();
        $matchingItemIds = $profile->getMatchingItemIds();
        $ids             = array_intersect($matchingItemIds, $selected);

        try {
            list($content, $ids) = $this->helperData->generateLiquidTemplate($profile, $ids, false, true);
            $this->helperData->createProfileFile('quickexport', $content);
            $this->historyFactory->create()->addData([
                'profile_id'      => $profile->getId(),
                'name'            => $profile->getName(),
                'generate_status' => 'Success',
                'type'            => 'Quick Export',
                'file'            => '',
                'product_count'   => count($ids),
                'message'         => ''
            ])->save();

            return $this->fileFactory->create(
                $profile->getFileName() . '.' . $this->helperData->getFileType($profile->getFileType()),
                ['type' => 'filename', 'value' => 'mageplaza/order_export/profile/quickexport', 'rm' => true],
                'media'
            );
        } catch (Exception $e) {
            $this->historyFactory->create()->addData([
                'profile_id'      => $profile->getId(),
                'name'            => $profile->getName(),
                'generate_status' => 'Error',
                'type'            => 'Quick Export',
                'file'            => '',
                'product_count'   => 0,
                'message'         => ''
            ])->save();
        }
        $resultRedirect->setPath($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
