<?php
namespace Magento\GiftCard\Block\Cart;

use Magento\Captcha\Block\Captcha;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Coupon extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     * @codeCoverageIgnore
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
        $this->scopeConfig = $scopeConfig;
    }

    public function isGiftCardEnabled()
    {
        return $this->scopeConfig->getValue('giftcard_section_id/general/enable', ScopeInterface::SCOPE_STORE) == 1;
    }

    public function isGiftCardEnabledUse()
    {
        return $this->scopeConfig->getValue('giftcard_section_id/general/checkout', ScopeInterface::SCOPE_STORE) == 1;
    }
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }
}
