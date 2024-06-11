<?php
namespace Magento\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\GiftCard\Model\GiftCardFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Checkout\Model\Session as CheckoutSession;


class CouponPostObserver implements ObserverInterface
{
    protected $cart;
    protected $messageManager;
    protected $responseRedirect;
    protected $ruleFactory;
    protected $itemFactory;
    protected $giftCardFactory;
    protected $redirect;
    protected $resultRedirectFactory;
    protected $checkoutSession;


    public function __construct(
        Cart $cart,
        ManagerInterface $messageManager,
        RedirectInterface $responseRedirect,
        RuleFactory $ruleFactory,
        ItemFactory $itemFactory,
        GiftCardFactory $giftCardFactory,
        RedirectInterface $redirect,
        RedirectFactory $resultRedirectFactory,
        CheckoutSession $checkoutSession
    ) {
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->responseRedirect = $responseRedirect;
        $this->ruleFactory = $ruleFactory;
        $this->itemFactory = $itemFactory;
        $this->giftCardFactory = $giftCardFactory;
        $this->redirect = $redirect;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(Observer $observer)
    {
//        $request = $observer->getEvent()->getRequest();
//        $couponCode = $request->getParam('coupon_code');
//        $controller = $observer->getEvent()->getControllerAction();
//
//        if (!empty($couponCode)) {
//            $quote = $this->cart->getQuote();
//            $giftCard = $this->giftCardFactory->create()->load($couponCode, 'code');
//            if ($giftCard->getId() && $giftCard->getTypeRedeem() == 0) {
//                $this->messageManager->addSuccessMessage(__('Gift card applied successfully.'));
//                $this->checkoutSession->setCustomCouponCode($couponCode);
//                $controller->getActionFlag()->set('', Action::FLAG_NO_DISPATCH, true);
//                $this->redirect->redirect($controller->getResponse(), 'checkout/cart');
//            }
//        }
    }
}
