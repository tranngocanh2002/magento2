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

namespace Mageplaza\OrderExport\Helper;

use DateTime;
use DateTimeZone;
use Exception;
use Liquid\Template;
use Magento\Backend\Model\Session;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime as FwDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusCollection;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\OrderExport\Block\Adminhtml\LiquidFilters;
use Mageplaza\OrderExport\Block\Liquid\Tag\TagFor;
use Mageplaza\OrderExport\Model\Config\Source\ExportType;
use Mageplaza\OrderExport\Model\Config\Source\FieldsSeparate;
use Mageplaza\OrderExport\Model\Config\Source\FileType;
use Mageplaza\OrderExport\Model\Profile;
use Magento\Framework\Filesystem\Driver\File as DriverFile;
use Magento\Framework\Filesystem\DriverInterface;
use phpseclib3\Net\SFTP as LibSFTP;

/**
 * Class Data
 * @package Mageplaza\OrderExport\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'mp_order_export';
    const XML_PATH_EMAIL     = 'email';
    const PROFILE_FILE_PATH  = BP . '/pub/media/mageplaza/order_export/profile';

    /**
     * @var File
     */
    protected $file;

    /**
     * @var LiquidFilters
     */
    protected $liquidFilters;

    /**
     * @var Ftp
     */
    protected $ftp;

    /**
     * @var Sftp
     */
    protected $sftp;

    /**
     * @var FwDateTime
     */
    protected $date;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var CreditmemoCollectionFactory
     */
    protected $creditmemoCollectionFactory;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var DriverFile
     */
    protected $driverFile;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var OrderStatusCollection
     */
    protected $orderStatusCollection;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param OrderFactory $orderFactory
     * @param OrderStatusCollection $orderStatusCollection
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param CreditmemoCollectionFactory $creditmemoCollectionFactory
     * @param GroupRepositoryInterface $groupRepository
     * @param Ftp $ftp
     * @param Sftp $sftp
     * @param CurlFactory $curlFactory
     * @param JsonHelper $jsonHelper
     * @param FwDateTime $date
     * @param DirectoryList $directoryList
     * @param File $file
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     * @param LiquidFilters $liquidFilters
     * @param DriverFile $driverFile
     * @param ManagerInterface $messageManager
     * @param Session $session
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        OrderFactory $orderFactory,
        OrderStatusCollection $orderStatusCollection,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        CreditmemoCollectionFactory $creditmemoCollectionFactory,
        GroupRepositoryInterface $groupRepository,
        Ftp $ftp,
        Sftp $sftp,
        CurlFactory $curlFactory,
        JsonHelper $jsonHelper,
        FwDateTime $date,
        DirectoryList $directoryList,
        File $file,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver,
        LiquidFilters $liquidFilters,
        DriverFile $driverFile,
        ManagerInterface $messageManager,
        Session $session,
        CountryFactory $countryFactory
    ) {
        $this->orderFactory                = $orderFactory;
        $this->invoiceCollectionFactory    = $invoiceCollectionFactory;
        $this->shipmentCollectionFactory   = $shipmentCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->groupRepository             = $groupRepository;
        $this->file                        = $file;
        $this->liquidFilters               = $liquidFilters;
        $this->ftp                         = $ftp;
        $this->sftp                        = $sftp;
        $this->date                        = $date;
        $this->jsonHelper                  = $jsonHelper;
        $this->directoryList               = $directoryList;
        $this->curlFactory                 = $curlFactory;
        $this->localeDate                  = $localeDate;
        $this->locale                      = $localeResolver->getLocale();
        $this->countryFactory              = $countryFactory;
        $this->driverFile                  = $driverFile;
        $this->messageManager              = $messageManager;
        $this->session                     = $session;
        $this->orderStatusCollection       = $orderStatusCollection;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Send Http Request
     *
     * @param Profile $profile
     *
     * @return array
     * @throws FileSystemException
     */
    public function sendHttpRequest($profile)
    {
        $url           = $profile->getHttpUrl();
        $headersConfig = $profile->getHttpHeader() ? explode("\n", $profile->getHttpHeader()) : [];

        $headers = [];
        foreach ($headersConfig as $item) {
            $key   = $item;
            $value = '';
            if (strpos($item, ':') !== false) {
                [$key, $value] = explode(':', $key);
                $header[trim($key)] = trim($value);
            }
            $header[trim($key)] = trim($value);
        }

        $fileName = $profile->getLastGeneratedFile();
        $filePath = $this->getFilePath($fileName);
        $content  = $this->driverFile->fileGetContents($filePath);
        $curl     = $this->curlFactory->create();

        $curl->write('POST', $url, '1.1', $headers, $content);

        $result     = ['success' => false];
        $resultCurl = $curl->read();

        if (!empty($resultCurl)) {
            $result['status'] = $this->extractCode($resultCurl);
            if (isset($result['status']) && in_array($result['status'], [200, 201])) {
                $result['success'] = true;
            } else {
                $result['message'] = __('Cannot connect to server. Please try again later.');
            }
        } else {
            $result['message'] = __('Cannot connect to server. Please try again later.');
        }

        $curl->close();

        return $result;
    }

    /**
     * Extract the response code from a response string
     *
     * @param string $response_str
     * @return int
     */
    public function extractCode($response_str)
    {
        preg_match("|^HTTP/[\d\.x]+ (\d+)|", $response_str, $m);

        if (isset($m[1])) {
            return (int) $m[1];
        } else {
            return false;
        }
    }

    /**
     * Get Email Config
     *
     * @param string $code
     * @param int $storeId
     *
     * @return mixed
     */
    public function getEmailConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig(self::XML_PATH_EMAIL . $code, $storeId);
    }

    /**
     * Check Connection
     *
     * @param string $protocol
     * @param string $host
     * @param int $passive
     * @param string $user
     * @param string $pass
     *
     * @return bool|LibSFTP|true
     */
    public function checkConnection($protocol, $host, $passive, $user, $pass)
    {
        try {
            if ($protocol === 'sftp') {
                $connection = $this->connectToHost('sftp', $host, $passive, $user, $pass);

                return $connection->login($user, $pass);
            }

            return $this->connectToHost('ftp', $host, $passive, $user, $pass);
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());

            return false;
        }
    }

    /**
     * Connect To Host
     *
     * @param string $protocol
     * @param string $host
     * @param int $passive
     * @param string $user
     * @param string $pass
     * @param int $timeout
     *
     * @return LibSFTP|true
     * @throws LocalizedException
     */
    public function connectToHost($protocol, $host, $passive, $user, $pass, $timeout = Sftp::REMOTE_TIMEOUT)
    {
        try {
            if ($protocol === 'sftp') {
                if (strpos($host, ':') !== false) {
                    [$host, $port] = explode(':', $host, 2);
                } else {
                    $port = Sftp::SSH2_PORT;
                }

                return new LibSFTP($host, $port, $timeout);
            }

            $open = $this->ftp->open([
                'host'     => $host,
                'user'     => $user,
                'password' => $pass,
                'passive'  => $passive
            ]);

            return $open;
        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Delivery Profile
     *
     * @param Profile $profile
     *
     * @throws Exception
     */
    public function deliveryProfile($profile)
    {
        $host          = $profile->getHostName();
        $username      = $profile->getUserName();
        $password      = $profile->getPassword();
        $timeout       = '20';
        $passiveMode   = $profile->getPassiveMode();
        $fileName      = $profile->getLastGeneratedFile();
        $filePath      = $this->getFilePath($fileName);
        $directoryPath = trim($profile->getDirectoryPath());

        if (!$host || !$username || !$password) {
            throw new Exception(__('Please check the Delivery information again.'));
        }

        if ($directoryPath && strripos($directoryPath, '/') !== (strlen($directoryPath) - 1)) {
            $directoryPath .= '/';
        }

        $directoryPath .= $fileName;

        if ($profile->getUploadType() === 'sftp') {
            // Fix Magento bug in 2.1.x
            $content    = $this->driverFile->fileGetContents($filePath);
            $mode       = DriverInterface::isReadable($content)
                ? \phpseclib\Net\SFTP::SOURCE_LOCAL_FILE
                : \phpseclib\Net\SFTP::SOURCE_STRING;
            $connection = $this->connectToHost('sftp', $host, $passiveMode, $username, $password, $timeout);
            if (!$connection->login($username, $password)) {
                throw new Exception(__("Unable to open SFTP connection as %1@%2", $username, $password));
            }
            $connection->put($directoryPath, $content, $mode);
            $connection->disconnect();
        } else {
            $this->connectToHost('ftp', $host, $passiveMode, $username, $password);
            $content = $this->driverFile->fileGetContents($filePath);
            $this->ftp->write($directoryPath, $content);
            $this->ftp->close();
        }
    }

    /**
     * Generate Liquid Template
     *
     * @param Profile $profile
     * @param array $ids
     * @param bool $preview
     * @param bool $quickExport
     * @param bool $isUseCache
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function generateLiquidTemplate(
        $profile,
        $ids = [],
        $preview = false,
        $quickExport = false,
        $isUseCache = false
    ) {
        $template       = new Template;
        $filtersMethods = $this->liquidFilters->getFiltersMethods();
        $template->registerFilter($this->liquidFilters);
        if ($isUseCache) {
            $template->registerTag('for', TagFor::class);
        }

        $profileType = $profile->getProfileType();
        $fileType    = $profile->getFileType();

        if ($isUseCache) {
            $collection   = [];
            $maxItemCount = $this->getProfileSessionData($profile->getId(), 'object_max_items');
            $ids          = [];
        } else {
            [$collection, $maxItemCount] = $this->getProfileData($profile, $ids, $preview, $quickExport);
            $ids = $collection->getAllIds();
        }

        if ($fileType === FileType::EXCEL_XML || $fileType === FileType::XML || $fileType === FileType::JSON) {
            $templateHtml = $profile->getTemplateHtml();
        } else {
            $fieldSeparate = $this->getFieldSeparate($profile->getFieldSeparate());
            $fieldAround   = $profile->getFieldAround() === 'none' ? ''
                : ($profile->getFieldAround() === 'quote' ? "'" : '"');
            $includeHeader = $profile->getIncludeHeader();
            $fieldsMap     = $this->jsonHelper->jsonDecode($profile->getFieldsList() ?: '{}');
            if (empty($fieldsMap)) {
                return ['', []];
            }
            if ((int) $profile->getExportType() === ExportType::LOOP_ORDER) {
                $row = [];
                foreach ($fieldsMap as $field) {
                    if ($field['col_type'] === 'item' && isset($field['items'])) {
                        foreach ($field['items'] as $item) {
                            $row[0][]      = $item['name'];
                            $itemLiquidVal = '{{ item.' . $item['value'];
                            if (isset($item['modifiers'])) {
                                foreach ($item['modifiers'] as $modifier) {
                                    $itemLiquidVal .= ' | ' . $modifier['value'];
                                    if (isset($modifier['params'])) {
                                        $itemLiquidVal .= ': ';
                                        foreach ($modifier['params'] as $key => $param) {
                                            if ($key === (count($modifier['params']) - 1)) {
                                                $itemLiquidVal .= $param;
                                            } else {
                                                $itemLiquidVal .= $param . ', ';
                                            }
                                        }
                                    }
                                }
                            }
                            $itemLiquidVal .= ' }}';
                            $row[1][]      = $fieldAround . $itemLiquidVal . $fieldAround;
                        }
                    } else {
                        $row[0][] = $field['col_name'];
                        if ($field['col_type'] === 'attribute') {
                            $row[1][] = $fieldAround . $field['col_val'] . $fieldAround;
                        } else {
                            $row[1][] = $fieldAround . $field['col_pattern_val'] . $fieldAround;
                        }
                    }
                }
                $row[0] = implode($fieldSeparate, $row[0]);
                $row[1] = implode($fieldSeparate, $row[1]);

                if ($includeHeader) {
                    $templateHtml = $row[0] . '
' . '{% for ' . $profileType . ' in collection %}{% for item in ' . $profileType . '.sub_items %}' . $row[1] . '
{% endfor %}{% endfor %}';
                } else {
                    $templateHtml = '{% for ' . $profileType . ' in collection %}{% for item in ' . $profileType . '.sub_items %}' . $row[1] . '
{% endfor %}{% endfor %}';
                }
            } else {
                $row = [];
                foreach ($fieldsMap as $field) {
                    if ($field['col_type'] === 'item' && isset($field['items'])) {
                        $items          = $field['items'];
                        $liquidItemsVal = [];

                        for ($i = 1; $i <= $maxItemCount; $i++) {
                            foreach ($items as $item) {
                                $row[0][] = 'item ' . $i . '(' . $item['name'] . ')';
                            }
                        }
                        foreach ($items as $item) {
                            $liquidVal = '';
                            if ($item) {
                                $liquidVal .= '{{ item.' . $item['value'];
                                if (isset($item['modifiers'])) {
                                    foreach ($item['modifiers'] as $modifier) {
                                        $liquidVal .= ' | ' . $modifier['value'];
                                        if (isset($modifier['params'])) {
                                            $liquidVal .= ': ';
                                            foreach ($modifier['params'] as $key => $param) {
                                                if ($key === (count($modifier['params']) - 1)) {
                                                    $liquidVal .= $param;
                                                } else {
                                                    $liquidVal .= $param . ', ';
                                                }
                                            }
                                        }
                                    }
                                }
                                $liquidVal .= ' }}';
                            }
                            $liquidItemsVal[] = $fieldAround . $liquidVal . $fieldAround;
                        }
                        $itemVariableCount = count($items);
                        $liquidItemsVal    = implode($fieldSeparate, $liquidItemsVal);
                        $liquidItemsVal    = '{% for item in ' . $profileType . '.sub_items %}{% if forloop.last == true %}'
                            . $liquidItemsVal . '{% else %}' . $liquidItemsVal . $fieldSeparate .
                            '{% endif %}{% endfor %}{% if '
                            . $profileType . '.items.size < maxItemCount %}{% for n in (1..' . $itemVariableCount
                            . ') %}{% for i in (' . $profileType . '.items.size..maxItemCount1) %}'
                            . $fieldSeparate . $fieldAround . $fieldAround . '{% endfor %}{% endfor %}{% endif %}';
                        $row[1][]          = $liquidItemsVal;
                        continue;
                    }

                    $row[0][] = $field['col_name'];
                    if ($field['col_type'] === 'attribute') {
                        $row[1][] = $fieldAround . $field['col_val'] . $fieldAround;
                    } else {
                        $row[1][] = $fieldAround . $field['col_pattern_val'] . $fieldAround;
                    }
                }

                $row[0] = implode($fieldSeparate, $row[0]);
                $row[1] = implode($fieldSeparate, $row[1]);

                if ($includeHeader) {
                    $templateHtml = $row[0] . '
' . '{% for ' . $profileType . ' in collection %}' . $row[1] . '
{% endfor %}';
                } else {
                    $templateHtml = '{% for ' . $profileType . ' in collection %}' . $row[1] . '
{% endfor %}';
                }
            }
        }
        $templateHtml = str_replace('}}', "| mpCorrect: '" . $profile->getFieldAround()
            . "', '" . $profile->getFieldSeparate() . "'}}", $templateHtml);
        array_push($filtersMethods, 'mpCorrect');

        $template->parse($templateHtml, $filtersMethods);
        $content = $template->render([
            'collection'    => $collection,
            'maxItemCount'  => $maxItemCount,
            'maxItemCount1' => $maxItemCount - 1,
            'profile_id'    => $profile->getId()
        ]);

        return [$content, $ids];
    }

    /**
     * Generate Profile
     *
     * @param Profile $profile
     * @param bool $skipCondition
     * @param bool $isUseCache
     *
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function generateProfile($profile, $skipCondition = false, $isUseCache = false)
    {
        [$content, $ids] = $this->generateLiquidTemplate($profile, [], false, false, $isUseCache);
        $profile->setLastGenerated($this->date->date());
        $fileName = $profile->getFileName();
        if ($profile->getAddTimestamp()) {
            $convertedDate = $this->localeDate->date(
                new DateTime('now', new DateTimeZone('UTC')),
                $this->locale,
                true
            );
            $fileName      .= '_' . $convertedDate->format('Ymd_His');
        }
        $fileName .= '.' . $this->getFileType($profile->getFileType());
        $this->createProfileFile($fileName, $content);
        $profile->setLastGeneratedFile($fileName);
        if (!$skipCondition) {
            $profileCount = $isUseCache
                ? $this->getProfileSessionData($profile->getId(), 'object_count') : count($ids);
            $profile->setLastGeneratedProductCount($profileCount);
            if ($isUseCache && empty($ids)) {
                $ids = $profile->getMatchingItemIds();
            }
            if (!$profile->getExportDuplicate()) {
                $exportedIds = $profile->getExportedIds();
                $exportedIds = $exportedIds ? explode(',', $exportedIds) : [];
                $exportedIds = array_unique(array_merge($exportedIds, $ids));
                $profile->setExportedIds(implode(',', $exportedIds));
            }
            if (!empty($ids) &&
                ($profile->getProfileType() === Profile::TYPE_ORDER)
                && ($changeStt = $profile->getChangeStt())
            ) {
                $orderStatusCollection = $this->orderStatusCollection->create()->joinStates();
                $orderStatusCollection->addAttributeToFilter('main_table.status', $changeStt);
                $orderState = $orderStatusCollection->getLastItem()->getState();
                $orderCollection = $this->orderFactory->create()->getCollection()
                    ->addFieldToFilter('entity_id', ['in' => $ids]);

                /** @var Order $order */
                foreach ($orderCollection as $order) {
                    if ($changeStt === Order::STATE_CANCELED) {
                        $order->setStatus($changeStt)->setState($changeStt);
                    } else {
                        if ($changeStt === Order::STATE_HOLDED) {
                            $order->setHoldBeforeState($order->getState());
                            $order->setHoldBeforeStatus($order->getStatus());
                        }
                        $order->setStatus($changeStt)->setState($orderState);
                    }
                    $order->save();
                }
            }
        }
        $profile->save();

        return count($ids);
    }

    /**
     * Create Profile's File
     *
     * @param string $fileName
     * @param string $content
     *
     * @throws Exception
     */
    public function createProfileFile($fileName, $content)
    {
        $this->file->checkAndCreateFolder(self::PROFILE_FILE_PATH);
        $fileUrl = self::PROFILE_FILE_PATH . '/' . $fileName;
        $this->file->write($fileUrl, $content, 0777);
    }

    /**
     * Process Request
     *
     * @param Profile $profile
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function processRequest($profile)
    {
        $step = $this->_getRequest()->getParam('step');
        if ($step === 'prepare_generate') {
            return $this->prepareGenerate($profile);
        }

        if ($step === 'prepare_product_data') {
            return $this->prepareObjectData($profile);
        }

        if ($step === 'render') {
            $this->generateProfile($profile, false, true);
            $this->messageManager->getMessages(true);
            $this->driverFile->deleteDirectory(self::PROFILE_FILE_PATH . 'collection/' . $profile->getId() . '/');

            return [
                'complete' => true,
            ];
        }

        return [
            'error'   => true,
            'message' => __('Something went wrong while generating feed')
        ];
    }

    /**
     * Prepare Generate
     *
     * @param Profile $profile
     *
     * @return array
     * @throws Exception
     */
    public function prepareGenerate($profile)
    {
        $exportLimit        = 1000;
        $profileExportLimit = $profile->getExportLimit();
        $configExportLimit  = (int) $this->getExportLimit();

        if ((int) $profileExportLimit) {
            $exportLimit = (int) $profileExportLimit;
        } elseif ($profileExportLimit === 'mp-use-config' && $configExportLimit) {
            $exportLimit = $configExportLimit;
        }

        $profileId = $profile->getId();
        $this->resetProfileSessionData($profileId);
        $itemIds = $profile->getMatchingItemIds();
        $chunk   = array_chunk($itemIds, $exportLimit);
        $this->setProfileSessionData($profileId, 'object_chunk', $chunk);
        $this->setProfileSessionData($profileId, 'object_max_items', 1);
        if ($this->file->checkAndCreateFolder(self::PROFILE_FILE_PATH . 'collection/' . $profileId)) {
            $this->driverFile->deleteDirectory(self::PROFILE_FILE_PATH . 'collection/' . $profileId . '/');
        }

        return [
            'object_count' => count($itemIds)
        ];
    }

    /**
     * Prepare Object Data
     *
     * @param Profile $profile
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function prepareObjectData($profile)
    {
        $profileId   = $profile->getId();
        $objectChunk = $this->getProfileSessionData($profileId, 'object_chunk');
        $objectCount = (int) $this->getProfileSessionData($profileId, 'object_count');
        $ids         = array_shift($objectChunk);
        [$collection, $maxItemCount] = $this->getProfileData($profile, $ids);

        if ($maxItemCount > $this->getProfileSessionData($profileId, 'object_max_items')) {
            $this->setProfileSessionData($profileId, 'object_max_items', $maxItemCount);
        }

        $objectCount += count($collection);
        $name        = $ids ? current($ids) . end($ids) : '0';
        $this->createProfileCollectionFile($profileId, self::jsonEncode($collection), $name);
        $this->setProfileSessionData($profileId, 'object_chunk', $objectChunk);
        $this->setProfileSessionData($profileId, 'object_count', $objectCount);

        return [
            'complete'     => empty($objectChunk),
            'object_count' => $objectCount
        ];
    }

    /**
     * Get Profile Session Data
     *
     * @param string|int $profileId
     * @param string $path
     *
     * @return mixed|null
     */
    public function getProfileSessionData($profileId, $path)
    {
        $data = $this->session->getData("mp_order_export_data_{$profileId}");

        return $data[$path] ?? null;
    }

    /**
     * Set Profile Session Data
     *
     * @param string|int $profileId
     * @param string $path
     * @param mixed $value
     */
    public function setProfileSessionData($profileId, $path, $value)
    {
        $data        = $this->session->getData("mp_order_export_data_{$profileId}");
        $data[$path] = $value;
        $this->session->setData("mp_order_export_data_{$profileId}", $data);
    }

    /**
     * Reset Profile Session Data
     *
     * @param string|int $profileId
     */
    public function resetProfileSessionData($profileId)
    {
        $this->session->setData("mp_order_export_data_{$profileId}", null);
    }

    /**
     * Get Profile Collection Paths
     *
     * @param string|int $profileId
     *
     * @return string[]
     * @throws FileSystemException
     */
    public function getProfileCollectionPaths($profileId)
    {
        $directoryUrl = self::PROFILE_FILE_PATH . 'collection/' . $profileId . '/';
        $paths        = $this->driverFile->readDirectory($directoryUrl);

        usort($paths, function ($pathA, $pathB) {
            $pathArrayA = explode('/', $pathA);
            $valueA     = end($pathArrayA);

            $pathArrayB = explode('/', $pathB);
            $valueB     = end($pathArrayB);

            return $valueB < $valueA ? 1 : -1;
        });

        return $paths;
    }

    /**
     * Read File
     *
     * @param string $path
     *
     * @return bool|string
     */
    public function readFile($path)
    {
        return $this->file->read($path);
    }

    /**
     * Create Profile Collection File
     *
     * @param int|string $profileId
     * @param string $content
     * @param string $name
     *
     * @throws Exception
     */
    public function createProfileCollectionFile($profileId, $content, $name)
    {
        $this->file->checkAndCreateFolder(self::PROFILE_FILE_PATH . 'collection/' . $profileId);
        $fileUrl = self::PROFILE_FILE_PATH . 'collection/' . $profileId . '/' . $name;
        $this->file->write($fileUrl, $content);
    }

    /**
     * Get File Type
     *
     * @param string $fileType
     *
     * @return string
     */
    public function getFileType($fileType)
    {
        switch ($fileType) {
            case FileType::XML:
            case FileType::EXCEL_XML:
                return 'xml';
            case FileType::CSV:
                return 'csv';
            case FileType::TSV:
                return 'tsv';
            case FileType::JSON:
                return 'json';
            case FileType::ODS:
                return 'ods';
            case FileType::XLSX:
                return 'xlsx';
            default:
                return 'txt';
        }
    }

    /**
     * Get Field Separate
     *
     * @param string $fieldSeparate
     *
     * @return string
     */
    public function getFieldSeparate($fieldSeparate)
    {
        switch ($fieldSeparate) {
            case FieldsSeparate::TAB:
                return '\t';
            case FieldsSeparate::SEMICOLON:
                return ';';
            case FieldsSeparate::COLON:
                return ':';
            case FieldsSeparate::VERTICAL_BAR:
                return '|';
            default:
                return ',';
        }
    }

    /**
     * Get Profile Data
     *
     * @param Profile $profile
     * @param array $ids
     * @param bool $preview
     * @param bool $quickExport
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getProfileData($profile, $ids = [], $preview = false, $quickExport = false)
    {
        switch ($profile->getProfileType()) {
            case Profile::TYPE_INVOICE:
                $collection = $this->invoiceCollectionFactory->create();
                break;
            case Profile::TYPE_SHIPMENT:
                $collection = $this->shipmentCollectionFactory->create();
                break;
            case Profile::TYPE_CREDITMEMO:
                $collection = $this->creditmemoCollectionFactory->create();
                break;
            default:
                $collection = $this->orderFactory->create()->getCollection();
        }
        if ($preview) {
            $collection->setPageSize(5);
        } else {
            if ($ids) {
                $matchingItemIds = $ids;
            } else {
                $matchingItemIds = $profile->getMatchingItemIds();
            }

            if ($quickExport) {
                $matchingItemIds = $ids;
            }

            $collection->addFieldToFilter('entity_id', ['in' => $matchingItemIds]);
        }
        $maxItemCount = 0;
        /** @var $item Creditmemo
         */
        foreach ($collection as $item) {
            $subItems = [];
            if ($item->getShippingAddress()) {
                $country = $this->countryFactory->create()->loadByCode($item->getShippingAddress()->getCountryId());
                foreach ($item->getShippingAddress()->getStreet() as $key => $street) {
                    $item->getShippingAddress()->setData('street_' . $key, $street);
                }
                $item->getShippingAddress()->setData('country', $country->getName());
                $item->setData('shippingAddress', $item->getShippingAddress()->getData());
            }
            if ($item->getBillingAddress()) {
                $country = $this->countryFactory->create()->loadByCode($item->getBillingAddress()->getCountryId());
                foreach ($item->getBillingAddress()->getStreet() as $key => $street) {
                    $item->getBillingAddress()->setData('street_' . $key, $street);
                }
                $item->getBillingAddress()->setData('country', $country->getName());
                $item->setData('billingAddress', $item->getBillingAddress()->getData());
            }
            if (count($item->getItems()) > $maxItemCount) {
                $maxItemCount = count($item->getItems());
            }

            /** @var Item $it */
            foreach ($item->getItems() as $it) {
                $it->setStatus($it->getStatus());
                $attributesInfo = ($it->getProductOptions()
                    && isset($it->getProductOptions()['attributes_info'])) ?
                    $it->getProductOptions()['attributes_info'] : [];
                $attributeInfo  = [];
                if (!empty($attributesInfo)) {
                    foreach ($attributesInfo as $attrInfo) {
                        $attributeInfo[] = $attrInfo['label'] . ': ' . $attrInfo['value'];
                    }
                }
                $customizableOptions = ($it->getProductOptions()
                    && isset($it->getProductOptions()['options'])) ?
                    $it->getProductOptions()['options'] : [];
                $customizableOption  = [];
                if (!empty($customizableOptions)) {
                    foreach ($customizableOptions as $customOption) {
                        $customizableOption[] = $customOption['label'] . ': ' . $customOption['value'];
                    }
                }
                $attributeInfo      = implode(', ', $attributeInfo);
                $customizableOption = implode(', ', $customizableOption);

                $it->setData('attributes', $attributeInfo);
                $it->setData('customizable_options', $customizableOption);
                $subItems[] = $it->getData();
            }
            $item->setData('sub_items', $subItems);
            $item->setData('allVisibleItems', $item->getAllVisibleItems());
            $order = $item->getOrder();
            $item->setCreatedAt($this->coverDateTimeToLocal($item->getCreatedAt()));
            if ($profile->getProfileType() !== Profile::TYPE_ORDER) {
                $item->setCustomerFirstname($order->getCustomerFirstname());
                $item->setCustomerLastname($order->getCustomerLastname());
                $item->setCustomerEmail($order->getCustomerEmail());
                $item->setShippingDescription($order->getShippingDescription());
                $item->setPaymentMethod($order->getPayment()->getMethod());
                $item->setStoreName($order->getStoreName());
                $item->setOrderDate($this->coverDateTimeToLocal($order->getCreatedAt()));
                $item->setCustomerGroup($this->groupRepository->getById($order->getCustomerGroupId())->getCode());
            }
            switch ($profile->getProfileType()) {
                case Profile::TYPE_INVOICE:
                    $item->setStateName($item->getStateName());

                    break;
                case Profile::TYPE_SHIPMENT:
                    $item->setOrderStatus($order->getStatus());
                    break;
                case Profile::TYPE_CREDITMEMO:
                    foreach ($item->getItems() as $creditmemoItem) {
                        if (!$creditmemoItem->getDiscountAmount()) {
                            $creditmemoItem->setDiscountAmount(0);
                        }
                    }
                    $item->setStateName($item->getStateName());
                    $item->setOrderStatus($order->getStatus());
                    break;
                default:
                    $item->setPaymentMethod($item->getPayment()->getMethod());
                    $item->setCustomerGroup($this->groupRepository->getById($item->getCustomerGroupId())->getCode());
            }
        }

        return [$collection, $maxItemCount];
    }

    /**
     * Covert Date Time To Local
     *
     * @param string $time
     *
     * @return string
     * @throws Exception
     */
    public function coverDateTimeToLocal($time)
    {
        $convertedDate = $this->localeDate->date(
            new DateTime($time, new DateTimeZone('UTC')),
            $this->locale,
            true
        );

        return $convertedDate->format('M j, Y h:i:s A');
    }

    /**
     * Get File Path
     *
     * @param string $filename
     *
     * @return string
     * @throws FileSystemException
     */
    public function getFilePath($filename)
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA)
            . '/mageplaza/order_export/profile/' . $filename;
    }

    /**
     * Get Store By Id
     *
     * @param int $storeId
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStoreById($storeId)
    {
        return $this->storeManager->getStore($storeId);
    }

    /**
     * Read Attachment
     *
     * @param string $file
     *
     * @return bool|string
     * @throws FileSystemException
     */
    public function readAttachment($file)
    {
        $filePath = $this->directoryList->getPath(DirectoryList::MEDIA) . '/mageplaza/order_export/profile/' . $file;

        return $this->file->read($filePath);
    }

    /**
     * Get Export Limit
     *
     * @return mixed
     */
    public function getExportLimit()
    {
        return $this->getConfigGeneral('limit_export') ?? false;
    }
}
