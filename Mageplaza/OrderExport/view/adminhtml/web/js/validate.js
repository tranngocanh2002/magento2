/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderExport
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/validation'
], function ($) {
    'use strict';

    $.extend(true, $.validator.prototype, {
        checkForm: function () {
            var elements = this.currentElements = this.elements(), i;

            $('.admin__page-nav-item-message._error').hide();
            this.prepareForm();
            for (i = 0; elements[i]; i++) {
                this.check(elements[i]);
            }

            return this.valid();
        },
    });
});
