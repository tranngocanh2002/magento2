<?php
namespace Magento\GiftCard\Plugin;

use Magento\Checkout\Block\Cart\Coupon;
use Magento\Checkout\Model\Session as CheckoutSession;

class CartCouponPlugin
{
    protected $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject, $result)
    {
//        $quote = $subject->getQuote();
//        $couponCode = $quote->getOrigData('coupon_code');
//        $couponCode = $this->checkoutSession->getCustomCouponCode();
//        if ($couponCode) {
//            return $couponCode;
//        }

        return $result;
    }
}
