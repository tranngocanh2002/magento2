<?php

namespace Magento\GiftCard\Block\Checkout;

use Magento\Framework\View\Element\Template;

class Discount extends Template
{
    protected $checkoutSession;

    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

}
