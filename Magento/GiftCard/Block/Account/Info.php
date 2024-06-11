<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Block\Account;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Block\Form\Register;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;

/**
 * Dashboard Customer Info
 *
 * @api
 * @since 100.0.2
 */
class Info extends Template
{
    protected $scopeConfig;
    /**
     * Cached subscription object
     *
     * @var Subscriber
     */
    protected $_subscription;

    /**
     * @var SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var View
     */
    protected $_helperView;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    protected $_giftCardFactory;

    protected $resourceConnection;


    /**
     * Constructor
     *
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param SubscriberFactory $subscriberFactory
     * @param View $helperView
     * @param array $data
     */
    public function __construct(
        Context $context,
        CurrentCustomer $currentCustomer,
        SubscriberFactory $subscriberFactory,
        View $helperView,
        ResourceConnection $resourceConnection,
        \Magento\GiftCard\Model\GiftCardFactory $giftCardFactory,
        ScopeConfigInterface $scopeConfig,
        array $data = [],
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_helperView = $helperView;
        parent::__construct($context, $data);
        $this->_giftCardFactory = $giftCardFactory;
        $this->resourceConnection = $resourceConnection;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get the full name of a customer
     *
     * @return string full name
     */
    public function getName()
    {
        return $this->_helperView->getCustomerName($this->getCustomer());
    }

    /**
     * Get the url to change password
     *
     * @return string
     */
    public function getChangePasswordUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return Subscriber
     */
    public function getSubscriptionObject()
    {
        if (!$this->_subscription) {
            $this->_subscription = $this->_createSubscriber();
            $customer = $this->getCustomer();
            if ($customer) {
                $websiteId = (int)$this->_storeManager->getWebsite()->getId();
                $this->_subscription->loadByCustomer((int)$customer->getId(), $websiteId);
            }
        }
        return $this->_subscription;
    }

    /**
     * Gets Customer subscription status
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->getLayout()
            ->getBlockSingleton(Register::class)
            ->isNewsletterEnabled();
    }

    /**
     * Create new instance of Subscriber
     *
     * @return Subscriber
     */
    protected function _createSubscriber()
    {
        return $this->_subscriberFactory->create();
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }

    public function getGiftCardCodes()
    {
        $customerId = $this->currentCustomer->getCustomerId();

        $giftCardModel = $this->_giftCardFactory->create();
        $collection = $giftCardModel->getCollection();

        $collection->getSelect()->join(
            ['history' => $collection->getTable('giftcard_history')],
            'main_table.giftcard_id = history.giftcard_id',
            '*'
        );

        $collection->addFieldToFilter('history.customer_id', $customerId);
        $collection->getSelect()->order('history.action_time DESC');

//                dd($collection->getSelect()->__toString());


        return $collection;
    }



    public function getGiftCardBalance()
    {
        $customerId = $this->currentCustomer->getCustomerId();
        if (!$customerId) {
            return 0;
        }

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('customer_giftcard');

        $select = $connection->select()
            ->from($tableName, ['balance'])
            ->where('customer_id = ?', $customerId)
            ->limit(1);

        $balance = $connection->fetchOne($select);
        return $balance ? $balance : 0;
    }

    public function isGiftCardEnabled()
    {
//        dd($this->scopeConfig->getValue('giftcard_section_id/general/enable', ScopeInterface::SCOPE_STORE));
        return $this->scopeConfig->getValue('giftcard_section_id/general/redeem', ScopeInterface::SCOPE_STORE) == 1;
    }
}
