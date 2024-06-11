<?php
namespace Magento\GiftCard\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\GiftCard\Model\GiftCardFactory;
use Magento\GiftCard\Model\CustomerGiftFactory;
use Magento\GiftCard\Model\GiftCardHistoryFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Redeem extends Action
{
    protected $giftCardFactory;
    protected $customerGiftFactory;
    protected $giftCardHistoryFactory;
    protected $customerSession;
    protected $eventManager;
    protected $customerRepository;

    public function __construct(
        Context $context,
        GiftCardFactory $giftCardFactory,
        CustomerGiftFactory $customerGiftFactory,
        GiftCardHistoryFactory $giftCardHistoryFactory,
        Session $customerSession,
        ManagerInterface $eventManager,
        CustomerRepositoryInterface $customerRepository,
    ) {
        parent::__construct($context);
        $this->giftCardFactory = $giftCardFactory;
        $this->customerGiftFactory = $customerGiftFactory;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->customerSession = $customerSession;
        $this->eventManager = $eventManager;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

//        $title = 'REDEEM';
//        $customerName = 'John Doe';
//        $orderNumber = '100000001';
//
//        $this->eventManager->dispatch('custom_send_email_event', [
//            'customer_name' => $customerName,
//            'title' => $title,
//            'order_number' => $orderNumber
//        ]);
//        die(__METHOD__);


        if ($post && isset($post['gift'])) {
            $giftCardCode = $post['gift'];
//            dd($giftCardCode);
            $giftCard = $this->giftCardFactory->create()->load($giftCardCode, 'code');
//            dd($giftCard->getTypeRedeem());

            if ($giftCard->getId() && $giftCard->getTypeRedeem() == 0) {
                $amount = $giftCard->getAmountUsed();
                if ($amount > 0) {
                    $customerId = $this->customerSession->getCustomerId();
                    $giftHistory = $this->giftCardHistoryFactory->create();
                    $giftHistory->setData([
                        'giftcard_id' => $giftCard->getId(),
                        'customer_id' => $customerId,
                        'amount' => $amount,
                        'action' => 'Redeem',
                    ]);
                    $giftHistory->save();

                    $addGiftCard = $this->giftCardFactory->create()->load($giftCard->getId());
                    $addGiftCard->addData([
                        'type_redeem' => 1,
                        'amount_used' => 0,
                    ]);
                    $addGiftCard->save();

                    $customerGiftCard = $this->customerGiftFactory->create();
                    $existingCustomerGiftCard = $customerGiftCard->load($customerId, 'customer_id');

                    if ($existingCustomerGiftCard->getId()) {
                        $existingBalance = $existingCustomerGiftCard->getBalance();
                        $newBalance = $existingBalance + $amount;
                        $existingCustomerGiftCard->setBalance($newBalance);
                        $existingCustomerGiftCard->save();
                    } else {
                        $customerGiftCard->setData([
                            'customer_id' => $customerId,
                            'balance' => $amount,
                        ]);
                        $customerGiftCard->save();
                    }

                    $customer = $this->customerRepository->getById($customerId);
                    $customerEmail = $customer->getEmail();

                    $this->messageManager->addSuccessMessage(__('Gift card redeemed successfully.'));
                    $this->eventManager->dispatch('custom_send_email_event', [
                        'title' => 'REDEEM GIFT CARD',
                        'code' => $giftCardCode,
                        'balance' => $amount,
                        'email' => $customerEmail,
                    ]);

                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
                else {
                    $this->messageManager->addErrorMessage(__('The value of the Gift Card Code has been used up'));
                }
            } else {
                // Add an error message if the gift card code is invalid
                $this->messageManager->addErrorMessage(__('Invalid gift card code or has been redeem.'));
            }
        } else {
            // Add an error message if no gift card code is provided
            $this->messageManager->addErrorMessage(__('Please enter a gift card code.'));
        }

        // Redirect back to the previous page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
