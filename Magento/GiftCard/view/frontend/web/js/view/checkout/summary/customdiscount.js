define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',

],
    function ($, Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_GiftCard/checkout/summary/customdiscount'
            },
            quoteIsVirtual: quote.isVirtual(),
            totals: quote.getTotals(),
            isDisplayedCustomdiscount: function () {

                let price = 0;
                let arrSegments = this.totals()['total_segments'];
                for (let i = 0; i < arrSegments.length; i++) {
                    if (arrSegments[i]['code'] === 'discount') price = arrSegments[i]['value'];
                }
                if (price)
                    return true;
                return false;
            },
            getCustomDiscount: function () {

                let price = 0;
                let checkDiscount = 0;
                let checkSubtotal = 0;
                let arrSegments = this.totals()['total_segments'];
                for (let i = 0; i < arrSegments.length; i++) {
                    if (arrSegments[i]['code'] === 'subtotal') checkSubtotal = Math.round(parseFloat(arrSegments[i]['value']));
                    if (arrSegments[i]['code'] === 'discount') checkDiscount = arrSegments[i]['value'];
                }
                if (checkSubtotal == -checkDiscount) {
                    price = Number(this.totals()['subtotal']) + Number(this.totals()['discount_amount']);
                } else {
                    price = -checkDiscount;
                }

                return this.getFormattedPrice(-price);
            },
            // getCouponLabel: function () {
            //     if (!this.totals()) {
            //         return null;
            //     }
            //
            //     return this.totals()['coupon_label'];
            // },
        });
    }
);
