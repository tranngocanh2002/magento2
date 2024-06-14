<?php
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

namespace Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Url;
use Mageplaza\OrderExport\Helper\Data;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class General
 * @package Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    /**
     * @var Enabledisable
     */
    protected $enabledisable;

    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Url $url
     * @param Enabledisable $enableDisable
     * @param Yesno $yesno
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Url $url,
        Enabledisable $enableDisable,
        Yesno $yesno,
        Data $helperData,
        array $data = []
    ) {
        $this->enabledisable = $enableDisable;
        $this->yesno         = $yesno;
        $this->url           = $url;
        $this->helperData    = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var Profile $profile */
        $profile = $this->_coreRegistry->registry('mageplaza_orderexport_profile');
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('profile_');
        $form->setFieldNameSuffix('profile');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('General Information'),
            'class'  => 'fieldset-wide'
        ]);
        if ($profile->getId()) {
            $fieldset->addField('id', 'hidden', [
                'name' => 'id',
            ]);
        }
        $fieldset->addField('profile_type', 'hidden', [
            'name'  => 'profile_type',
            'value' => $this->_request->getParam('type') ? $this->_request->getParam('type') : 'order'
        ]);

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->enabledisable->toOptionArray(),
            'value'  => 1
        ]);

        $fieldset->addField('file_name', 'text', [
            'name'     => 'file_name',
            'label'    => __('File Name'),
            'title'    => __('File Name'),
            'required' => true
        ]);
        $fieldset->addField('add_timestamp', 'select', [
            'name'   => 'add_timestamp',
            'label'  => __('Add timestamp to file name'),
            'title'  => __('Add timestamp to file name'),
            'note'   => __(
                'The timestamp will be added to the end of file name.
                You will keep all the exported files on the server.
                If no, the old file with the same name will be overridden'
            ),
            'values' => $this->yesno->toOptionArray()
        ]);

        $exportLimit = !$profile->getId() || $profile->getExportLimit() === 'mp-use-config' ? 'checked' : '';
        if ($exportLimit) {
            $profile->setExportLimit($this->helperData->getExportLimit());
        }
        $fieldset->addField('export_limit', 'text', [
            'name'               => 'export_limit',
            'label'              => __('Export Limit'),
            'title'              => __('Export Limit'),
            'container_id'       => 'row_form_export_limit',
            'class'              => 'validate-number validate-zero-or-greater validate-digits',
            'after_element_html' =>
                '<input type="checkbox"
                class="mp-use-config"
                id="export-limit-use-config"
                name="profile[export_limit]"
                value="mp-use-config" ' . $exportLimit . '>' .
                __('Use Config'),
        ]);

        $href = $profile->getId()
            ? $this->url->getUrl(
                'mporderexport/download',
                ['id' => $profile->getId(), 'secretkey' => $profile->getSecretKey(), '_nosid' => true]
            )
            : '';
        $fieldset->addField('download_url', 'link', [
            'name'  => 'download_url',
            'label' => __('Private Download URL'),
            'title' => __('Private Download URL'),
            'href'  => $href,
            'value' => $href,
        ]);
        $fieldset->addField('secret_key_clone', 'text', [
            'name'               => 'secret_key',
            'label'              => __('Secret Key'),
            'title'              => __('Secret Key'),
            'style'              => 'width: 40%; margin-right:1%',
            'value'              => $profile->getSecretKey() ?: $key = hash('md5', time()),
            'disabled'           => true,
            'after_element_html' => '<a style="width: 15%;" id="reset-secret-key" class="btn primary '
                . ($profile->getId() ? '' : 'loading') . '">' . __('Reset Key') . '</a>',
        ]);
        $fieldset->addField('secret_key', 'hidden', [
            'name'  => 'secret_key',
            'value' => isset($key) ? $key : ''
        ]);
        $fieldset->addField('last_generated_product_count', 'label', [
            'name'  => 'last_generated_product_count',
            'label' => __('Number of exported orders'),
            'title' => __('Number of exported orders'),
        ]);
        $scheduleFieldset = $form->addFieldset('schedule_fieldset', [
            'legend' => __('Schedule'),
            'class'  => 'fieldset-wide'
        ]);
        $autoRun          = $scheduleFieldset->addField('auto_run', 'select', [
            'name'   => 'auto_run',
            'label'  => __('Auto run profile'),
            'title'  => __('Auto run profile'),
            'values' => $this->yesno->toOptionArray()
        ]);
        $cronSchedule     = $scheduleFieldset->addField('cron_schedule', 'text', [
            'name'  => 'cron_schedule',
            'label' => __('Cron schedule'),
            'title' => __('Cron schedule'),
            'class' => 'validate-cron-format',
            'note'  => __('How to config <a href="https://www.mageplaza.com/faqs/how-configure-cronjob.html" target="_blank">cron</a>')
        ]);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Dependence::class)
                ->addFieldMap($autoRun->getHtmlId(), $autoRun->getName())
                ->addFieldMap($cronSchedule->getHtmlId(), $cronSchedule->getName())
                ->addFieldDependence($cronSchedule->getName(), $autoRun->getName(), '1')
        );
        $form->addValues($profile->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = parent::toHtml();

        $url = $this->getUrl(
            'mporderexport/manageprofiles/generate',
            ['id' => $this->getRequest()->getParam('id')]
        );

        $html .= '<script type="text/x-magento-init">{"#generate":{"Mageplaza_OrderExport/js/profile/generate":{"url":"'
            . $url . '"}}}</script>';

        return $html;
    }
}
