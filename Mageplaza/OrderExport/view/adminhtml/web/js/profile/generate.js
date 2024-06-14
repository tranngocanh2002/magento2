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
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert'
], function ($, $t, modal, uiConfirm, uiAlert) {
    "use strict";

    $.widget('mageplaza.generateProfile', {
        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.initGenerateProfileObs();
            this.initUseConfig();
            this.useConfigCheckedObs();
        },

        initUseConfig: function () {
            $('.mp-use-config:checked').each(function () {
                var enableEl = $(this).parents('.addafter').siblings();

                enableEl.prop('disabled', true);
            });
        },

        useConfigCheckedObs: function () {
            $('.mp-use-config').on('click', function () {
                var enableEl = $(this).parents('.addafter').siblings();

                enableEl.prop('disabled', $(this).is(':checked'));
            });
        },

        initGenerateProfileObs: function () {
            var self = this;

            this.element.click(function () {
                var prepareGenerateEl,
                    prepareProductsEl;

                self.initProcessModal();
                prepareGenerateEl = $('#mp_prepare_generated');
                prepareProductsEl = $('#mp_prepare_products_data');
                self.mpStopRequest = false;

                prepareGenerateEl.find('.loader-small').show();

                $.ajax({
                    url: self.options.url,
                    method: 'POST',
                    data: {form_key: window.FORM_KEY, step: 'prepare_generate'},
                    success: function (res) {
                        if (!res.success) {
                            self.showErrorMessage(res.message ? res.message : res);
                            self.generateModal.closeModal();
                            return;
                        }
                        prepareGenerateEl.find('.index-icon').html('<i class="fa fa-check" style="color: green"></i>');
                        prepareGenerateEl.find('.loader-small').hide();
                        prepareProductsEl.find('.index-process').html(
                            '(<span class="current-count">0</span>/<span class="total-count">'
                            + res.object_count + '</span>)');
                        self.prepareProductData();
                    },
                    error: function (e) {
                        self.mpStopRequest = true;
                        self.showErrorMessage(e.responseText);
                        self.generateModal.closeModal();
                    }
                });
            });
        },

        prepareProductData: function () {
            var self              = this,
                prepareProductsEl = $('#mp_prepare_products_data');

            if (this.mpStopRequest) {
                return;
            }
            prepareProductsEl.find('.loader-small').show();

            $.ajax({
                url: self.options.url,
                method: 'POST',
                data: {form_key: window.FORM_KEY, step: 'prepare_product_data'},
                success: function (res) {
                    if (!res.success) {
                        self.showErrorMessage(res.message ? res.message : res);
                        self.generateModal.closeModal();
                        return;
                    }

                    prepareProductsEl.find('.index-process .current-count').text(res.object_count);
                    if (res.complete) {
                        prepareProductsEl.find('.index-icon').html('<i class="fa fa-check" style="color: green"></i>');
                        prepareProductsEl.find('.loader-small').hide();
                        self.generateProfile();
                    } else {
                        self.prepareProductData();
                    }
                },
                error: function (e) {
                    self.mpStopRequest = true;
                    self.showErrorMessage(e.responseText);
                    self.generateModal.closeModal();
                }
            });
        },

        generateProfile: function () {
            var self     = this,
                renderEl = $('#mp_render');

            if (this.mpStopRequest) {
                return;
            }
            renderEl.find('.loader-small').show();

            $.ajax({
                url: self.options.url,
                method: 'POST',
                data: {form_key: window.FORM_KEY, step: 'render'},
                success: function (res) {
                    if (!res.success) {
                        self.showErrorMessage(res.message ? res.message : res);
                        self.generateModal.closeModal();
                        return;
                    }

                    self.generateModal.setTitle($t('Generate Completed'));
                    self.generateModal.buttons.first().text($t('Ok'));
                    self.generateModal.buttons.first().data('mpIsComplete', true);
                    renderEl.find('.index-icon').html('<i class="fa fa-check" style="color: green"></i>');
                    renderEl.find('.loader-small').hide();
                    $('#profile_tabs_profile_content').html(res.general_html);
                    $('#profile_tabs_profile_content .field-last_generated_product_count .control-value').html(res.object_count);
                    self.initUseConfig();
                    self.useConfigCheckedObs();
                },
                error: function (e) {
                    self.mpStopRequest = true;
                    self.showErrorMessage(e.responseText);
                    self.generateModal.closeModal();
                    history_gridJsObject.reload();
                }
            });
        },

        initProcessModal: function () {
            var html = '', options, self = this;

            html += '<div>';
            html +=
                '<div class="mp-index-item" id="mp_prepare_generated">' +
                '   <div class="index-icon" style="display: inline-block; width: 20px;"></div>' +
                '   <div class="loader-small"></div>' +
                '   <span class="index-title">' + $t('Prepare Generate') + '</span>' +
                '   <span class="index-process"></span> ' +
                '</div>';
            html +=
                '<div class="mp-index-item" id="mp_prepare_products_data">' +
                '   <div class="index-icon" style="display: inline-block; width: 20px;"></div>' +
                '   <div class="loader-small"></div>' +
                '   <span class="index-title">' + $t('Prepare Products Data') + '</span>' +
                '   <span class="index-process"></span>' +
                '</div>';
            html +=
                '<div class="mp-index-item" id="mp_render">' +
                '   <div class="index-icon" style="display: inline-block; width: 20px;"></div>' +
                '   <div class="loader-small"></div>' +
                '   <span class="index-title">' + $t('Generate Profile') + '</span>' +
                '   <span class="index-process"></span> ' +
                '</div>';

            html += '</div>';

            options            = {
                'type': 'popup',
                'modalClass': 'mp-generate-popup',
                'title': $t('Generating Profile...'),
                'responsive': true,
                'innerScroll': true,
                'buttons': [{
                    text: $t('Cancel'),
                    class: 'action',
                    click: function () {
                        var that = this;

                        if (this.buttons.first().data('mpIsComplete')) {
                            that.closeModal();
                        } else {
                            uiConfirm({
                                content: $t('Are you sure to stop generate?'),
                                actions: {
                                    /** @inheritdoc */
                                    confirm: function () {
                                        self.mpStopRequest = true;
                                        that.closeModal();
                                    }
                                }
                            });
                        }
                    }
                }],
                'modalCloseBtnHandler': function () {
                    self.mpStopRequest = true;
                    this.closeModal();
                }
            };
            this.generateModal = modal(options, html);
            this.generateModal.openModal().on('modalclosed', function () {
                $('.mp-generate-popup').remove();
            });
        },
        showErrorMessage: function (mess) {
            uiAlert({
                content: mess
            });
        }
    });

    return $.mageplaza.generateProfile;
});
