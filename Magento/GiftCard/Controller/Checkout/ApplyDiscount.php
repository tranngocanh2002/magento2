<?php

namespace Magento\GiftCard\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\SalesRule\Model\CouponFactory;
use Magento\Quote\Api\CartRepositoryInterface;

class ApplyDiscount extends Action
{
    protected $checkoutSession;
    protected $redirectFactory;
    protected $couponFactory;
    protected $quoteRepository;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        RedirectFactory $redirectFactory,
        CouponFactory $couponFactory,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->redirectFactory = $redirectFactory;
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $couponCode = $this->getRequest()->getParam('discount_code');
        $quote = $this->checkoutSession->getQuote();
        $quote->setCouponCode($couponCode)->collectTotals();
        $this->quoteRepository->save($quote);

        if ($quote->getCouponCode() == $couponCode) {
            $this->messageManager->addSuccessMessage(__('Discount code applied successfully.'));
        } else {
            $this->messageManager->addErrorMessage(__('Discount code is not valid.'));
        }

        $resultRedirect = $this->redirectFactory->create();
        return $resultRedirect->setPath('checkout');
    }
}
