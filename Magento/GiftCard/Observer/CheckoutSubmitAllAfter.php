<?php

namespace Magento\GiftCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\GiftCard\Model\GiftCardFactory;
use Magento\GiftCard\Model\GiftCardHistoryFactory;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\ManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;



class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    protected $checkoutSession;
    protected $giftCardFactory;
    protected $giftCardHistoryFactory;
    protected $eventManager;
    protected $customerRepository;


    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        CheckoutSession $checkoutSession,
        GiftCardFactory $giftCardFactory,
        GiftCardHistoryFactory $giftCardHistoryFactory,
        ManagerInterface $eventManager,
        CustomerRepositoryInterface $customerRepository,
    )
    {
//        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer);
//        $logger->info('Hello World!');
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->eventManager = $eventManager;
        $this->customerRepository = $customerRepository;

    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        // Get the orders from the observer
        $couponCode = $this->checkoutSession->getCustomCouponCode();
        if (!empty($couponCode)) {
            $giftCard = $this->giftCardFactory->create()->load($couponCode, 'code');
            $giftCardAmount = $giftCard->getAmountUsed();
            $giftCardId = $giftCard->getGiftcardId();

            $order = $observer->getEvent()->getOrder();
            $TotalAmount = $order->getData('subtotal');
            if ($giftCardAmount >= $TotalAmount) {
                $discountAmount = -$TotalAmount;
            } else {
                $discountAmount = -$giftCardAmount;
            }

            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(json_encode($order->getData()));
            $logger->info('$giftCardAmount' . $giftCardAmount);
            $logger->info('$TotalAmount' . $TotalAmount);
            $logger->info('$discountAmount' . $discountAmount);

            $giftCardHistory = $this->giftCardHistoryFactory->create();
            $giftCardHistory->setData([
                'giftcard_id' => $giftCardId,
                'customer_id' => $order->getData('customer_id'),
                'amount' => $discountAmount,
                'action' => 'Use for order #' . $order->getData('increment_id')
            ]);
            $giftCardHistory->save();

            $giftCard = $this->giftCardFactory->create()->load($giftCardId);
            $amountUsed = $giftCard->getData('amount_used') + $discountAmount;
            $giftCard->addData([
                'amount_used' => $amountUsed
            ]);
            $giftCard->save();

            $customer = $this->customerRepository->getById($order->getData('customer_id'));
            $customerEmail = $customer->getEmail();

            $this->eventManager->dispatch('custom_send_email_event', [
                'title' => 'USE GIFT CARD',
                'code' => $couponCode,
                'balance' => $discountAmount,
                'email' => $customerEmail,
            ]);
            $this->checkoutSession->unsCustomCouponCode();
        }


//        $this->checkoutSession->unsCustomCouponCode();

    }
}
