<?php
namespace Mageplaza\BetterWishlist\Plugin;

use Magento\Catalog\Block\Product\View;

class ChangeDefaultQtyPlugin
{
    protected $productView;

    public function __construct(
        View $productView
    ) {
        $this->productView = $productView;
    }

    public function aroundGetAddToCartQty(
        \Magento\Wishlist\Block\Customer\Wishlist\Item\Column\Cart $subject,
        callable $proceed,
        \Magento\Wishlist\Model\Item $item
    ) {
//        return 0;
    }
}
