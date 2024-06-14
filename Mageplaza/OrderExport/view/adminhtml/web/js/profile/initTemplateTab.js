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
    'underscore',
    'Mageplaza_OrderExport/js/lib/codemirror/lib/codemirror',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'Mageplaza_OrderExport/js/lib/codemirror/mode/xml/xml',
    'Mageplaza_OrderExport/js/lib/codemirror/mode/javascript/javascript',
    'Mageplaza_OrderExport/js/lib/codemirror/addon/display/autorefresh',
    'Mageplaza_OrderExport/js/lib/codemirror/addon/mode/overlay',
    'jquery/ui'
], function ($, _, CodeMirror, modal, $t, uiAlert) {
    "use strict";

    var loadTempBtn = $('#load-template');
    var fieldsColEl = $('#fields-map .fields-col');
    var attrSelectHtml = $('#select-attr').html();
    var itemAttrSelectHtml = $('#item-select-attr').html();
    var insertVarPopup = $('#insert-variable-popup');
    var fieldMap = $('#fields-map');

    $.widget('mageplaza.initTemplateTab', {
        options: {
            modifierOptHtml: '<option value="0">' + $t("--Please Select--") + '</option>',
            itemAttributesOptHtml: itemAttrSelectHtml
        },
        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            var self = this;
            _.each(self.options.modifiersData, function (record, index) {
                self.options.modifierOptHtml += '<option value="' + index + '">' + record.label + '</option>';
            });

            this.initLoadTemplate();
            this.initCodeMirror();
            this.initPopup();
            this.initObservable();
            this.initDragable();
            this.checkDeliveryConnection();
            this.initFieldsMap();
            this.initUseConfig();
            this.useConfigCheckedObs();
        },
        initObservable: function () {
            this.onInsertVarPopChange();
            this.resetSecretKey();
            this.resetExported();
            this.preview();
            this.initChangeFileType();
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
        preview: function () {
            var previewUrl = this.options.previewUrl;
            var previewBtn = $('#profile_preview');
            previewBtn.on('click', function () {
                previewBtn.val($t('Downloading')).attr('disabled', true);

                var form = $('<form>').attr({method: 'POST', action: previewUrl}).css({display: 'none'});
                var data = $('#edit_form').serializeArray();
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        form.append($('<input>').attr({
                            type: 'hidden',
                            name: String(data[key]['name']),
                            value: String(data[key]['value'])
                        }));
                    }
                }
                form.appendTo('body');
                form.submit();
                form.remove();

                previewBtn.val($t('Download first 5 items')).attr('disabled', false);
            });
        },
        resetExported: function () {
            var resetFlagUrl = this.options.resetFlagUrl;
            var resetFlagBtn = $('#reset-flag');
            resetFlagBtn.on('click', function () {
                $.ajax({
                    url: resetFlagUrl,
                    data: {id: $('#profile_id').val()},
                    type: 'POST',
                    beforeSend: function () {
                        resetFlagBtn.text($t('Resetting')).addClass('loading');
                    },
                    success: function (res) {
                        var message = $t('This profile has been reset');
                        if (!res) {
                            message = $t('Profile does not exits.');
                        }
                        uiAlert({
                            content: message
                        });
                    },
                    error: function (res) {
                        uiAlert({
                            content: res.statusText
                        });
                    },
                    complete: function () {
                        resetFlagBtn.text($t('Reset Flag')).removeClass('loading');
                    }
                });
            });
        },
        resetSecretKey: function () {
            $('#reset-secret-key').on('click', function () {
                var rankey = Math.random().toString(36).substring(2, 12)
                    + Math.random().toString(36).substring(2, 12)
                    + Math.random().toString(36).substring(2, 12)
                    + Math.random().toString(36).substring(2, 4);
                $('#profile_secret_key_clone').val(rankey);
                $('#profile_secret_key').val(rankey);
            });
        },
        initPopup: function () {
            var options = {
                type: 'slide',
                responsive: true,
                innerScroll: true,
                title: $t('Insert Variable'),
                buttons: []
            };
            modal(options, insertVarPopup);

            $('a#insert-variable').click(function () {
                insertVarPopup.modal('openModal');
            });
        },
        initDragable: function () {
            var self = this;
            $('#insert-variable-popup .modifier-group').sortable({
                stop: function () {
                    var attr_code = $(this).attr('code');
                    self.updateVariable(attr_code);
                }
            });
        },
        initLoadTemplate: function () {
            var self = this;
            var tplUrl = this.options.url;
            loadTempBtn.click(function () {
                loadTempBtn.text($t('Loading...')).addClass('loading');
                $.ajax({
                    url: tplUrl,
                    data: {name: $('#profile_default_template').val()},
                    type: 'POST',
                    success: function (res) {
                        $('#profile_field_separate').val(res.field_separate);
                        $('#profile_field_around').val(res.field_around);
                        $('#profile_include_header').val(res.include_header);
                        if (res.template_html && res.template_html !== '') {
                            $('#profile_template_html').val(res.template_html);
                            self.options.doc.setValue(res.template_html);
                        }
                        if (res.fields_list && res.fields_list !== '') {
                            $('#fields-map .fields-col').html('');
                            self.renderFieldsMap(JSON.parse(res.fields_list));
                        }
                        loadTempBtn.text($t('Load Template')).removeClass('loading');
                    }
                });
            });
        },
        initChangeFileType: function () {
            var self = this,
                fileType = $('#profile_file_type'),
                profileType = $('#profile_profile_type'),
                defaultTemplate = $("#profile_default_template");

            var updateDefaultTemplate = function () {
                if (fileType.val() === 'json') {
                    self.options.codeMirror.setOption("mode", 'javascript')
                }
                var hasDefaultTemplate = false;

                defaultTemplate.children().remove();
                $.each(self.options.defaultTemplate, function (key, template) {
                    if (fileType.val() === template.type && profileType.val() === template.profileType) {
                        defaultTemplate.append($("<option></option>")
                            .attr("value", key)
                            .text(template.label));

                        hasDefaultTemplate = true;
                    }
                });
                if (!hasDefaultTemplate) {
                    defaultTemplate.parents('.field-default_template').hide();
                } else {
                    defaultTemplate.parents('.field-default_template').show();
                }
            };
            fileType.on('change', function () {

                updateDefaultTemplate();
            });
            updateDefaultTemplate();
        },
        initCodeMirror: function () {
            var self = this;
            var profileTmpHtml = $('#profile_template_html');
            this.options.codeMirror = CodeMirror.fromTextArea(profileTmpHtml[0], {
                mode: 'xml',
                lineNumbers: true,
                autofocus: true,
                autoRefresh: true,
                styleActiveLine: true,
                viewportMargin: Infinity
            });
            this.options.codeMirror.addOverlay({
                token: function (stream) {
                    var query = /^{{.*?}}/g;
                    if (stream.match(query)) {
                        return 'liquid-variable';
                    }
                    stream.next();
                }
            });
            this.options.codeMirror.addOverlay({
                token: function (stream) {
                    var query = /^{%.*?%}/g;
                    if (stream.match(query)) {
                        return 'liquid-method';
                    }
                    stream.next();
                }
            });
            this.options.codeMirror.on('change', function (cMirror) {
                profileTmpHtml.val(cMirror.getValue());
            });
            this.options.doc = this.options.codeMirror.getDoc();
            $('.insert').on('click', function () {
                var cursor = self.options.doc.getCursor();
                self.options.doc.replaceRange($(this).siblings('.liquid-variable').text(), cursor);
                $('[data-role="closeBtn"].action-close').trigger('click');
            });
        },
        checkDeliveryConnection: function () {
            var checkBtn = $('#profile_check_connect'),
                mesEl = $('.check-connect-message'),
                checkConnectUrl = this.options.checkConnectionUrl;
            checkBtn.click(function () {
                var data = {
                    protocol: $('#profile_upload_type').val(),
                    host: $('#profile_host_name').val(),
                    passive: $('#profile_passive_mode').val(),
                    user: $('#profile_user_name').val(),
                    pass: $('#profile_password').val()
                };

                checkBtn.attr('disabled', true);
                checkBtn.val($t('Checking...'));
                $.ajax({
                    url: checkConnectUrl,
                    type: 'POST',
                    data: data,
                    success: function (res) {
                        if (res === 1) {
                            mesEl.html('<p style="color:green;margin-left: 20px">' + $t('The connection has succeeded.') + '</p>');
                        } else {
                            mesEl.html('<p style="color:red;margin-left: 20px">' + $t('The connection has failed.') + '</p>');
                        }
                    },
                    error: function (e) {
                        mesEl.html('<p style="color:red;margin-left: 20px">' + $t('The connection has failed.') + '</p>');
                    },
                    complete: function () {
                        checkBtn.attr('disabled', false);
                        checkBtn.val($t('Check Connection'));
                    }
                });
            });
        },

        onInsertVarPopChange: function () {
            insertVarPopup.on('change', 'select', this.rowSelectObs.bind(this));
            insertVarPopup.on('change', 'input', this.inputObs.bind(this));
            insertVarPopup.on('click', '.remove-modifier', this.removeModifierObs.bind(this));
            insertVarPopup.on('click', '.add-modifier', this.addModifierObs.bind(this));
        },

        rowSelectObs: function (e) {
            var elf = $(e.target);
            var elfVal = elf.val();
            var paramsEl = elf.siblings('.params');
            var attr_code = elf.parents('.modifier').attr('code');
            var paramHtml = '';
            if (elfVal !== "0") {
                _.each(this.options.modifiersData[elfVal].params, function (record) {
                    paramHtml += ('<span class="modifier-param">' + record.label + '</span><input class="modifier-param" type="text" code="' + attr_code + '"/>')
                });
            }
            paramsEl.html(paramHtml);
            this.updateVariable(attr_code);
        },
        inputObs: function (e) {
            this.updateVariable($(e.target).attr('code'));
        },
        removeModifierObs: function (e) {
            var el = $(e.target);
            var rowModifier = $(el).parents('.modifier-group');
            var attr_code = $(el).parents('.modifier').attr('code');
            $(el).parents('.modifier').remove();
            this.updateVariable(attr_code);

            if (!rowModifier.children().length) {
                rowModifier.parent().removeClass('show');
            }
        },
        addModifierObs: function (e) {
            var opt = this.options.modifierOptHtml;
            var el = $(e.target);
            var rowModifier = $(el).parent().siblings('.row-modifier');
            if (!rowModifier.hasClass('show')) {
                rowModifier.addClass('show');
            }
            var attr_code = $(el).parents('.attr-code').attr('code');
            var modifierEl = '<div class="modifier" code="{{attr_code}}">\n' +
                '    <div class="row">\n' +
                '        <select>\n' +
                '            {{options}}' +
                '        </select>\n' +
                '        <div class="params"></div><button class="remove-modifier">' + $t("Remove") + '</button>\n' +
                '    </div>\n' +
                '</div>';
            modifierEl = modifierEl
                .replace("{{attr_code}}", attr_code)
                .replace("{{options}}", opt);

            $('#' + attr_code).find('.modifier-group').append(modifierEl);
        },
        updateVariable: function (attr_code) {
            var parentEl = $('.attr-code[code="' + attr_code + '"]');
            var str = '{{ ';
            str += parentEl.parent().attr('attr-type') + '.' + attr_code;
            parentEl.find('.modifier').each(function () {
                var modifier = $(this).find('select').val();
                if (modifier && modifier !== '0') {
                    str += ' | ' + modifier;
                }
                var params = $(this).find('input.modifier-param');
                if (params.length) {
                    str += ': ';

                    params.each(function (index) {
                        if (index === (params.length - 1)) {
                            str += "'" + this.value + "'";
                            return;
                        }
                        str += "'" + this.value + "', ";
                    });
                }
            });
            str += ' }}';
            parentEl.find('.liquid-variable').text(str);
        },
        initFieldsMap: function () {
            this.addRowObs();
            this.onFieldMapChange();
            this.initFieldsMapDragable();
            this.renderFieldsMap(this.options.fieldsMap);
        },

        onFieldMapChange: function () {
            fieldMap.on('click', 'a.modifier-collapse', this.modiferCollapse.bind(this));
            fieldMap.on('click', 'a.remove-modifier', this.removeFieldsMapModifierObs.bind(this));
            fieldMap.on('change', '.modifier select', this.selectModifierObs.bind(this));
            fieldMap.on('click', 'a.col-remove', this.removeRowObs.bind(this));
            fieldMap.on('click', 'a.add-modifier', this.addFieldsMapModifierObs.bind(this));
            fieldMap.on('click', 'a.remove-item', this.removeItemObs.bind(this));
            fieldMap.on('click', 'a.add-item', this.addItemObs.bind(this));
            fieldMap.on('change', '.col-type select', this.selectTypeObs.bind(this));

            //changeValObs
            var self = this;
            fieldMap.on('change', '.col-value input,.modifier-group input,.col-value select', function () {
                var attrEl = $(this).parents('.field-col');
                self.updateFieldMapVariable(attrEl);
            });
            fieldMap.on('change', '.col-value select', function () {
                var name = $(this).val().replace(/\_/g, ' ').replace(/\b[a-z]/g, function (f) {
                    return f.toUpperCase();
                });
                $(this).parents('.field-col').find('.col-name input').val(name);
            });
            fieldMap.on('change', '.item-modifier-value', function () {
                var name = $(this).val().replace(/\_/g, ' ').replace(/\b[a-z]/g, function (f) {
                    return f.toUpperCase();
                });
                $(this).siblings('input').val(name);
            });
        },

        modiferCollapse: function (e) {
            var el = $(e.target);
            var parentEl = $(el).parents('.field-col');
            var i = $(el).find('i');
            if (parentEl.find('.col-type select').val() === 'attribute') {
                parentEl.find('.modifier-group').toggle();
            } else {
                parentEl.find('.item-container').toggle();
            }
            self.collapse(i);

        },
        removeFieldsMapModifierObs: function (e) {
            var el = $(e.target);
            var attrEl = $(el).parents('.field-col');
            $(el).parent().remove();
            this.updateFieldMapVariable(attrEl);
        },
        selectModifierObs: function (e) {
            var elf = $(e.target);
            var modifierId = elf.parents('.modifier').attr('id');
            var paramsEl = elf.siblings('.params');
            var attrEl = $('#' + modifierId).parents('.field-col');
            paramsEl.html('');
            if (elf.val() !== 0) {
                this.createModifierParams(modifierId);
            }
            this.updateFieldMapVariable(attrEl);
        },

        removeRowObs: function (e) {
            $(e.target).parents('.field-col').remove();
        },

        selectTypeObs: function (e) {
            var typeEl = $(e.target);
            var valEl = typeEl.parent().siblings('.col-value');
            if (typeEl.val() === 'attribute') {
                typeEl.parent().siblings('.col-add-modifier').show();
                typeEl.parent().siblings('.col-add-item').hide();
                typeEl.parent().siblings('.col-collapsible').css('visibility', 'visible');
                valEl.find('input').hide();
                valEl.find('select').show();
                typeEl.parent().siblings('.col-name').find('input').removeClass('disabled');
                typeEl.parents('.field-col').find('.modifier-group').show();
                typeEl.parents('.field-col').find('.item-container').hide();
            } else if (typeEl.val() === 'pattern') {
                typeEl.parent().siblings('.col-add-modifier').hide();
                typeEl.parent().parent().siblings('.modifier-group').hide();
                typeEl.parent().siblings('.col-add-item').hide();
                typeEl.parent().siblings('.col-collapsible').css('visibility', 'hidden');
                valEl.find('input').show();
                valEl.find('select').hide();
                typeEl.parent().siblings('.col-name').find('input').removeClass('disabled');
                typeEl.parents('.field-col').find('.modifier-group').hide();
                typeEl.parents('.field-col').find('.item-container').hide();
            } else {
                typeEl.parent().siblings('.col-add-modifier').hide();
                typeEl.parent().siblings('.col-collapsible').css('visibility', 'visible');
                valEl.find('input').hide();
                valEl.find('select').hide();
                typeEl.parent().siblings('.col-add-item').show();
                typeEl.parent().siblings('.col-name').find('input').addClass('disabled');
                typeEl.parents('.field-col').find('.modifier-group').hide();
                typeEl.parents('.field-col').find('.item-container').show();

            }
        },
        addRowObs: function () {
            var self = this;
            $('#add-column').click(function () {
                var d = new Date();
                var _id = d.getTime() + '_' + d.getMilliseconds();
                self.createRow(_id);
            });
        },
        initFieldsMapDragable: function () {
            var self = this;
            $('.fields-col').sortable();
            $('#fields-map .modifier-group').sortable({
                stop: function () {
                    var attrEl = $(this).parents('.field-col');
                    self.updateFieldMapVariable(attrEl);
                }
            });
        },
        createRow: function (_id) {
            var $html =
                '<div class="field-col row" id="' + _id + '">' +
                '    <div class="col-row row">' +
                '        <div class="col-drag"></div>' +
                '        <div class="col-name">' +
                '            <input type="text" name="profile[fields_list][' + _id + '][col_name]"/>' +
                '        </div>' +
                '        <div class="col-type">' +
                '            <select name="profile[fields_list][' + _id + '][col_type]">' +
                '                <option value="attribute">' + $t('Attribute') + '</option>' +
                '                <option value="pattern">' + $t('Pattern') + '</option>' +
                '                <option value="item">' + $t('Item') + '</option>' +
                '            </select>' +
                '        </div>' +
                '        <div class="col-value">' +
                '            <select name="profile[fields_list][' + _id + '][col_attr_val]">' + attrSelectHtml + '</select>' +
                '            <input name="profile[fields_list][' + _id + '][col_pattern_val]" type="text" class="pattern" style="display: none"/>' +
                '            <input name="profile[fields_list][' + _id + '][col_val]" type="hidden" class="liquid-variable">' +
                '        </div>' +
                '        <div class="col-collapsible">' +
                '            <a class="modifier-collapse"><i class="fa fa-chevron-down"></i></a>' +
                '        </div>' +
                '        <div class="col-remove">' +
                '            <a class="col-remove btn">' + $t('Remove') + '</a>' +
                '        </div>' +
                '        <div class="col-add-modifier">' +
                '            <a class="col-add-modifier add-modifier btn" rowId="' + _id + '" resourceName="profile[fields_list][' + _id + '][modifiers]">' + $t('Add Modifier') + '</a>' +
                '        </div>' +
                '        <div class="col-add-item" style="display: none">' +
                '            <a class="col-add-item add-item btn" rowId="' + _id + '" resourceName="profile[fields_list][' + _id + '][items]">' + $t('Add Item Attribute') + '</a>' +
                '        </div>' +
                '    </div>' +
                '    <div class ="modifier-group"></div>' +
                '    <div class ="item-container"></div>' +
                '</div>';
            fieldsColEl.append($html);
        },
        addFieldsMapModifierObs: function (e) {
            var el = $(e.target);
            var colEl = $(el).parents('.field-col');
            var typeVal = colEl.find('.col-type select').val();
            var modifierVal = 0;
            var modifierGroup = '';

            if (typeVal === 'attribute') {
                modifierVal = colEl.find('.col-value select').val();
                modifierGroup = '.modifier-group';
            } else if (typeVal === 'item') {
                modifierVal = $(el).parents('.item').find('select.item-modifier-value').val();
                modifierGroup = '.item-modifier-group';
            } else {
                return;
            }
            if (modifierVal == 0) {
                return;
            }

            var i = colEl.find('.col-collapsible i'),
                rowId = $(el).attr('rowId'),
                resourceName = $(el).attr('resourceName'),
                d = new Date(),
                _id = d.getTime() + '_' + d.getMilliseconds(),
                modifierGroupEl = $('#' + rowId).find(modifierGroup);
            this.createModifierRow(rowId, _id, resourceName, typeVal, modifierGroupEl);

            modifierGroupEl.show();
            if (i.hasClass('fa-chevron-down')) {
                i.removeClass('fa-chevron-down');
                i.addClass('fa-chevron-up');
            }
        },
        createModifierRow: function (rowId, _id, resourceName, type, modifierGroupEl) {
            var opt = this.options.modifierOptHtml,
                modifierEl =
                    '<div class="modifier" id="' + _id + '" name="' + resourceName + '[' + _id + ']">' +
                    '    <div class="row">' +
                    '        <select name="' + resourceName + '[' + _id + '][value]" id="' + resourceName + '[' + _id + ']">' +
                    '{{options}}' +
                    '        </select>' +
                    '        <div class="params"></div>' +
                    '        <a class="remove-modifier btn"><span>' + $t('Remove') + '</span></a>' +
                    '    </div>' +
                    '</div>';
            modifierEl = modifierEl.replace("{{options}}", opt);
            modifierGroupEl.append(modifierEl)
        },
        createModifierParams: function (modifierId, params) {
            var self = this,
                modifierName = $('#' + modifierId).attr('name'),
                paramsEl = $('#' + modifierId + ' .params'),
                attr_code = $('#' + modifierId + ' select').val(),
                params = params || {};

            if (attr_code == 0) {
                return;
            }

            _.each(self.options.modifiersData[attr_code].params, function (record, index) {
                paramsEl.append('<span class="modifier-param">' + record.label + '</span><input value="'
                    + (params[index] === undefined ? '' : params[index]) +
                    '" name="' + modifierName + '[params][' + index + ']" class="modifier-param" type="text"/>');
            });
        },

        collapse: function (i) {
            if (i.hasClass('fa-chevron-down')) {
                i.removeClass('fa-chevron-down');
                i.addClass('fa-chevron-up');
            } else {
                i.removeClass('fa-chevron-up');
                i.addClass('fa-chevron-down');
            }
        },
        updateFieldMapVariable: function (attrEl) {
            var attr_code = attrEl.find('.col-value select').val();
            var str = '';
            if (attr_code && attrEl.find('.col-type select').val() === 'attribute') {
                str = '{{ ';
                str += this.options.profileType + '.' + attr_code;
                attrEl.find('.modifier').each(function () {
                    var modifier = $(this).find('select').val();
                    if (modifier === "0") {
                        return;
                    }
                    str += ' | ' + modifier;
                    var params = $(this).find('input.modifier-param');
                    if (params.length) {
                        str += ': ';

                        params.each(function (index) {
                            if (index === (params.length - 1)) {
                                str += "'" + this.value + "'";
                                return;
                            }
                            str += "'" + this.value + "', ";
                        });
                    }
                });
                str += ' }}';
            }

            attrEl.find('input.liquid-variable').val(str);
        },
        renderFieldsMap: function (fieldsMap) {
            var self = this;
            _.each(fieldsMap, function (record, index) {
                if ((record.col_type === 'attribute' && record.col_attr_val === 0)
                    || (record.col_type === 'pattern' && record.col_pattern_val === '')
                ) {
                    return;
                }
                self.createRow(index);
                $('#' + index + ' .col-name input').val(record.col_name);
                $('#' + index + ' .col-value select').val(record.col_attr_val);
                $('#' + index + ' .col-value .pattern').val(record.col_pattern_val);
                $('#' + index + ' .col-value .liquid-variable').val(record.col_val);

                var resourceName = 'profile[fields_list][' + index + '][modifiers]';
                var modifierGroupEl = $('#' + index).find('.modifier-group');
                _.each(record.modifiers, function (modifier, key) {
                    if (modifier.value == 0) {
                        return;
                    }
                    self.createModifierRow(index, key, resourceName, 'attribute', modifierGroupEl);

                    $('#' + key + ' select').val(modifier.value);
                    if (modifier.params !== undefined) {
                        self.createModifierParams(key, modifier.params);
                    }
                });
                _.each(record.items, function (item, itemKey) {
                    if (item.value == 0) {
                        return;
                    }
                    self.createItemRow(index, itemKey, item.name);
                    $('#' + itemKey + ' select').val(item.value);
                    var itemResourceName = 'profile[fields_list][' + index + '][items][' + itemKey + '][modifiers]';
                    var itemModifierGroupEl = $('#' + itemKey).find('.item-modifier-group');
                    _.each(item.modifiers, function (itemModifier, itemModifierKey) {
                        if (itemModifier.value == 0) {
                            return;
                        }
                        self.createModifierRow(itemKey, itemModifierKey, itemResourceName, 'item', itemModifierGroupEl);
                        $('#' + itemModifierKey + ' select').val(itemModifier.value);
                        if (itemModifier.params !== undefined) {
                            self.createModifierParams(itemModifierKey, itemModifier.params);
                        }
                    });
                });
                $('#' + index + ' .col-type select').val(record.col_type).trigger('change');
                self.updateFieldMapVariable($('#' + index));
            });
        },
        addItemObs: function (e) {
            var el = $(e.target);
            var i = $(el).parents('.field-col').find('.col-collapsible i');
            var rowId = $(el).attr('rowId');
            var d = new Date();
            var _id = d.getTime() + '_' + d.getMilliseconds();
            this.createItemRow(rowId, _id);

            var itemContainerEl = $('#' + rowId).find('.item-container');
            itemContainerEl.show();
            if (i.hasClass('fa-chevron-down')) {
                i.removeClass('fa-chevron-down');
                i.addClass('fa-chevron-up');
            }
        },
        createItemRow: function (rowId, _id, name) {
            var opt = this.options.itemAttributesOptHtml,
                itemContainerEl = $('#' + rowId).find('.item-container'),
                resourceName = 'profile[fields_list][' + rowId + '][items][' + _id + ']',
                itemEl =
                    '<div class="item" id="' + _id + '" name="' + resourceName + '">' +
                    '   <div class="row">' +
                    '   <input class="item-name" type="text" name="' + resourceName + '[name]" value="' + name + '">' +
                    '       <select class="item-modifier-value" name="' + resourceName + '[value]">' +
                    opt +
                    '       </select>' +
                    '       <a class="remove-item btn">' + $t('Remove') + '</a>' +
                    '       <a class="add-modifier btn" rowId="' + _id + '" resourceName="' + resourceName + '[modifiers]">' + $t('Add Modifier') + '</a>' +
                    '   </div>' +
                    '   <div class="item-modifier-group"></div>' +
                    '</div>';
            itemContainerEl.append(itemEl)
        },
        removeItemObs: function (e) {
            $(e.target).parents('.item').remove();
        }
    });

    return $.mageplaza.initTemplateTab;
});
