<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCard\Controller\Cart;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\GiftCard\Model\GiftCardFactory;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponPost extends \Magento\Checkout\Controller\Cart implements HttpPostActionInterface
{
    protected $giftCardFactory;

    protected $checkoutSession;

    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        GiftCardFactory $giftCardFactory,
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
        $this->giftCardFactory = $giftCardFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('remove_giftcard') == 1) {
            $this->checkoutSession->unsCustomCouponCode();
        }
        $couponCode = $this->getRequest()->getParam('coupon_code', '');

        $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
        $coupon = $this->giftCardFactory->create()->load($couponCode, 'code');

//        if ($isCodeLengthValid && $coupon->getId() && $coupon->getTypeRedeem() == 0) {
//            $this->checkoutSession->setCustomCouponCode($couponCode);
//            $this->messageManager->addSuccessMessage(
//                __(
//                    'You used coupon code "%1".',
//                    $escaper->escapeHtml($couponCode)
//                )
//            );
//        } else {
//            $this->messageManager->addErrorMessage(
//                __(
//                    'The coupon code "%1" is not valid.',
//                    $escaper->escapeHtml($couponCode)
//                )
//            );
//        }


        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode() ?? '';

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return $this->_goBack();
        }

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
//            if ($itemsCount) {
////                die();
//                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
//                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
//                $this->quoteRepository->save($cartQuote);
//            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                $coupon = $this->giftCardFactory->create()->load($couponCode, 'code');
//                $coupon->load($couponCode, 'code');
//                dd($itemsCount);
                if (!$itemsCount) {
                    if ($isCodeLengthValid && $coupon->getId()) {
                        $this->checkoutSession->setCustomCouponCode($couponCode);
                        $this->messageManager->addSuccessMessage(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                } else {
//                    dd($couponCode);
                    if ($isCodeLengthValid && $coupon->getId() && $coupon->getTypeRedeem() == 0) {
                        $this->checkoutSession->setCustomCouponCode($couponCode);
                        $this->messageManager->addSuccessMessage(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                }
            } else {
                $this->messageManager->addSuccessMessage(__('You canceled the coupon code.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot apply the coupon code.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return $this->_goBack();
    }
}
