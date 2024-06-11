<?php
namespace Magento\GiftCard\Model\Total\Quote;

use Magento\GiftCard\Model\GiftCardFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $eventManager;
    protected $storeManager;
    protected $calculator;
    protected $priceCurrency;
    protected $giftCardFactory;
    protected $checkoutSession;
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        GiftCardFactory $giftCardFactory,
        CheckoutSession $checkoutSession,
        UrlInterface $urlBuilder
    ) {
        $this->setCode('testdiscount');
        $this->eventManager = $eventManager;
        $this->calculator = $validator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->giftCardFactory = $giftCardFactory;
        $this->checkoutSession = $checkoutSession;
        $this->urlBuilder = $urlBuilder;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
//        dd($total->getData());
//        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer);
//        $logger->info(json_encode($total->getData()));

        parent::collect($quote, $shippingAssignment, $total);
        $couponCode = $this->checkoutSession->getCustomCouponCode();

        if (!empty($couponCode)) {
            $giftCard = $this->giftCardFactory->create()->load($couponCode, 'code');
            $giftCardAmount = $giftCard->getAmountUsed();
            $TotalAmount = $total->getData('subtotal') + $total->getData('discount_amount');


            if ($giftCardAmount >= $TotalAmount) {
                $discountAmount = -$TotalAmount;
            } else {
                $discountAmount = -$giftCardAmount;
            }
            $total->addTotalAmount('customdiscount', $discountAmount);
            $total->addBaseTotalAmount('customdiscount', $discountAmount);

            return $this;
        }
    }


    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
//        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer);
//        $logger->info(json_encode($this->getCode()));

        $couponCode = $this->checkoutSession->getCustomCouponCode();
        $giftCard = $this->giftCardFactory->create()->load($couponCode, 'code');
        $giftCardAmount = $giftCard->getAmountUsed();
        $TotalAmount = $total->getSubtotal();
        if ($giftCardAmount >= $TotalAmount) {
            $discountAmount = -$TotalAmount;
        } else {
            $discountAmount = -$giftCardAmount;
        }
        $description = (string)$total->getDiscountDescription() ?: '';
        return [
            'code' => 'discount',
            'title_gift' => strlen($description) ? __('abc (%1)', $description) : __('abc'),
            'value' => $discountAmount
        ];
    }
}
