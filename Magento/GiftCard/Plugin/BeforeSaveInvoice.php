<?php
namespace Magento\GiftCard\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\GiftCard\Model\GiftCardFactory;
//use Magento\GiftCard\Model\CustomerGiftFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\GiftCard\Model\GiftCardHistoryFactory;
use Magento\Sales\Controller\Adminhtml\Order\Invoice\Save as InvoiceSaveController;

class BeforeSaveInvoice
{
    protected $orderRepository;
    protected $giftCardFactory;
//    protected $customerGiftFactory;
    protected $resourceConnection;
    protected $giftCardHistoryFactory;
    protected $scopeConfig;
    protected $customerRepository;
    protected $eventManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        GiftCardFactory $giftCardFactory,
//        CustomerGiftFactory $customerGiftFactory,
        ResourceConnection $resourceConnection,
        GiftCardHistoryFactory $giftCardHistoryFactory,
        ScopeConfigInterface $scopeConfig,
        CustomerRepositoryInterface $customerRepository,
        ManagerInterface $eventManager,
    ) {
        $this->orderRepository = $orderRepository;
        $this->giftCardFactory = $giftCardFactory;
//        $this->customerGiftFactory = $customerGiftFactory;
        $this->resourceConnection = $resourceConnection;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
        $this->eventManager = $eventManager;
    }

    public function beforeExecute(InvoiceSaveController $subject)
    {
        $request = $subject->getRequest();
        $orderId = $request->getParam('order_id');

        $order = $this->orderRepository->get($orderId);
        $customerId = $order->getCustomerId();
        $is_virtual = $order->getData('is_virtual');
        if ($is_virtual == 1) {
            $items = $order->getAllVisibleItems();
            $itemQty = 0;

            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('catalog_product_entity_varchar');
            $attributeId = 158;
//            $storeId = $order->getStoreId();
//            dd($order['increment_id']);

            foreach ($items as $item) {
                $productId = $item->getProductId();

                $select = $connection->select()
                    ->from(['cpev' => $tableName], ['value'])
                    ->where('cpev.entity_id = ?', $productId)
                    ->where('cpev.attribute_id = ?', $attributeId)
                    ->limit(1);
                $value = $connection->fetchOne($select);

                $itemQty = $item->getQtyOrdered();
                if ($value > 0) {
                    for ($i = 0; $i < $itemQty; $i++) {
                        $giftCard = $this->giftCardFactory->create();
                        $code_length = $this->scopeConfig->getValue('giftcard_section_id/general_test/code_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        $code = $this->generateRandomCode($code_length);
                        $balance = $item->getData('price');
                        $giftCard->setData([
                            'code' => $code,
                            'balance' => $balance,
                            'amount_used' => $value,
                            'create_from' => $customerId
                        ]);
                        $giftCard->save();

                        $giftCardHistory = $this->giftCardHistoryFactory->create();
                        $giftCardHistory->setData([
                            'giftcard_id' => $giftCard->getId(),
                            'customer_id' => $customerId,
                            'amount' => $value,
                            'action' => 'Created for order #'.$order['increment_id']
                        ]);
                        $giftCardHistory->save();

                        $customer = $this->customerRepository->getById($customerId);
                        $customerEmail = $customer->getEmail();
                        $this->eventManager->dispatch('custom_send_email_event', [
                            'title' => 'CREATE GIFT CARD',
                            'code' => $code,
                            'balance' => $balance,
                            'email' => $customerEmail,
                        ]);
                    }
                }
            }
        }

    }

    private function generateRandomCode($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        if (!is_numeric($length) || $length > 36) {
            $length = 36;
        }
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
