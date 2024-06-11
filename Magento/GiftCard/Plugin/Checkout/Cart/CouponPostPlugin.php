<?php
namespace Magento\GiftCard\Plugin\Checkout\Cart;

use Magento\Checkout\Controller\Cart\CouponPost;
use Magento\Framework\Message\ManagerInterface;
use Magento\GiftCard\Model\GiftCardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;

class CouponPostPlugin
{
    protected $messageManager;
    protected $giftCardFactory;
    protected $resultRedirectFactory;
    protected $cart;
    protected $checkoutSession;

    public function __construct(
        ManagerInterface $messageManager,
        GiftCardFactory $giftCardFactory,
        RedirectFactory $resultRedirectFactory,
        Cart $cart,
        CheckoutSession $checkoutSession
    ) {
        $this->messageManager = $messageManager;
        $this->giftCardFactory = $giftCardFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
    }

    public function aroundExecute(CouponPost $subject, callable $proceed)
    {
////        dd($subject->getRequest()->getParam('remove'));
////        die();
//        $checkRemove = $subject->getRequest()->getParam('remove');
//        if ($checkRemove == 1) {
//            $this->checkoutSession->unsCustomCouponCode();
//        }
        return $proceed();
    }
}
