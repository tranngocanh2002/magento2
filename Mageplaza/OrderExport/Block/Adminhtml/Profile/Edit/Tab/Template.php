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
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab\Renderer\TemplateContent;
use Mageplaza\OrderExport\Model\Config\Source\DefaultTemplate;
use Mageplaza\OrderExport\Model\Config\Source\ExportType;
use Mageplaza\OrderExport\Model\Config\Source\FieldsAround;
use Mageplaza\OrderExport\Model\Config\Source\FieldsSeparate;
use Mageplaza\OrderExport\Model\Config\Source\FileType;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class Template
 * @package Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab
 */
class Template extends Generic implements TabInterface
{
    /**
     * @var Yesno
     */
    protected $yesNo;

    /**
     * @var FileType
     */
    protected $fileType;

    /**
     * @var DefaultTemplate
     */
    protected $defaultTemplate;

    /**
     * @var FieldsSeparate
     */
    protected $fieldsSeparate;

    /**
     * @var FieldsAround
     */
    protected $fieldsAround;

    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var ExportType
     */
    protected $exportType;

    /**
     * Template constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param FieldFactory $fieldFactory
     * @param Yesno $yesNo
     * @param FileType $fileType
     * @param DefaultTemplate $defaultTemplate
     * @param FieldsSeparate $fieldsSeparate
     * @param FieldsAround $fieldsAround
     * @param ExportType $exportType
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        FieldFactory $fieldFactory,
        Yesno $yesNo,
        FileType $fileType,
        DefaultTemplate $defaultTemplate,
        FieldsSeparate $fieldsSeparate,
        FieldsAround $fieldsAround,
        ExportType $exportType,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->fieldFactory    = $fieldFactory;
        $this->fileType        = $fileType;
        $this->defaultTemplate = $defaultTemplate;
        $this->fieldsSeparate  = $fieldsSeparate;
        $this->fieldsAround    = $fieldsAround;
        $this->yesNo           = $yesNo;
        $this->exportType      = $exportType;
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Profile $profile */
        $profile = $this->_coreRegistry->registry('mageplaza_orderexport_profile');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('profile_');
        $form->setFieldNameSuffix('profile');

        $fieldset   = $form->addFieldset('template_fieldset', [
            'legend' => __('Templates'),
            'class'  => 'fieldset-wide'
        ]);
        $fileType   = $fieldset->addField('file_type', 'select', [
            'name'     => 'file_type',
            'label'    => __('File Type'),
            'title'    => __('File Type'),
            'required' => true,
            'values'   => $this->fileType->toOptionArray(),
            'disabled' => $profile->getId() ? 1 : 0,
            'note'     => __('Select a file type for the profile')
        ]);
        $exportType = $fieldset->addField('export_type', 'select', [
            'name'   => 'export_type',
            'label'  => __('Export Type'),
            'title'  => __('Export Type'),
            'values' => $this->exportType->toOptionArray(),
            'note'   => __('Select export type for the profile')
        ]);
        if (!$profile->getId()) {
            $fieldset->addField('default_template', 'select', [
                'name'               => 'enabled',
                'label'              => __('Default Template'),
                'title'              => __('Default Template'),
                'values'             => $this->defaultTemplate->toOptionArray(),
                'after_element_html' => '<a id="load-template" class="btn primary">' . __('Load Template') . '</a>',
                'note'               => __('Select a template for the profile')
            ]);
        }

        /** @var RendererInterface $rendererBlock */
        $rendererBlock = $this->getLayout()->createBlock(TemplateContent::class);
        $templateHtml  = $fieldset->addField('template_html', 'textarea', [
            'name'  => 'template_html',
            'label' => __('Template Content'),
            'title' => __('Template Content'),
            'note'  => __('Supports <a href="https://shopify.github.io/liquid/" target="_blank">Liquid template</a>')
        ])->setRenderer($rendererBlock);

        $rendererBlock = $this->getLayout()
            ->createBlock(TemplateContent::class)
            ->setTemplate('Mageplaza_OrderExport::profile/template/fields_list.phtml');
        $fieldMap      = $fieldset->addField('fields_list', 'text', [
            'name'  => 'fields_list',
            'label' => __('Fields List'),
            'title' => __('Fields List'),
        ])->setRenderer($rendererBlock);

        $fieldSeparate = $fieldset->addField('field_separate', 'select', [
            'name'   => 'field_separate',
            'label'  => __('Separated by'),
            'title'  => __('Separated by'),
            'values' => $this->fieldsSeparate->toOptionArray(),

        ]);
        $fieldAround   = $fieldset->addField('field_around', 'select', [
            'name'   => 'field_around',
            'label'  => __('Around by'),
            'title'  => __('Around by'),
            'values' => $this->fieldsAround->toOptionArray(),

        ]);
        $includeHeader = $fieldset->addField('include_header', 'select', [
            'name'   => 'include_header',
            'label'  => __('Show Column Header'),
            'title'  => __('Show Column Header'),
            'values' => $this->yesNo->toOptionArray(),
        ]);
        $fieldset->addField('preview', 'button', [
            'name'  => 'preview',
            'title' => __('Download first 5 items'),
            'value' => __('Download first 5 items'),
            'class' => 'action-default primary btn'
        ]);
        $refField    = $this->fieldFactory->create([
            'fieldData'   => ['value' => 'csv,txt,tsv,ods,xlsx', 'separator' => ','],
            'fieldPrefix' => ''
        ]);
        $xmlRefField = $this->fieldFactory->create([
            'fieldData'     => [
                'value'     => 'xml,excel_xml,json',
                'separator' => ','
            ],
            'fieldPrefix' => ''
        ]);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Dependence::class)
                ->addFieldMap($fileType->getHtmlId(), $fileType->getName())
                ->addFieldMap($templateHtml->getHtmlId(), $templateHtml->getName())
                ->addFieldMap($fieldSeparate->getHtmlId(), $fieldSeparate->getName())
                ->addFieldMap($fieldAround->getHtmlId(), $fieldAround->getName())
                ->addFieldMap($includeHeader->getHtmlId(), $includeHeader->getName())
                ->addFieldMap($fieldMap->getHtmlId(), $fieldMap->getName())
                ->addFieldMap($exportType->getHtmlId(), $exportType->getName())
                ->addFieldDependence($templateHtml->getName(), $fileType->getName(), $xmlRefField)
                ->addFieldDependence($fieldSeparate->getName(), $fileType->getName(), $refField)
                ->addFieldDependence($exportType->getName(), $fileType->getName(), $refField)
                ->addFieldDependence($fieldAround->getName(), $fileType->getName(), $refField)
                ->addFieldDependence($includeHeader->getName(), $fileType->getName(), $refField)
                ->addFieldDependence($fieldMap->getName(), $fileType->getName(), $refField)
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
        return __('Templates');
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
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        $formHtml  = parent::getFormHtml();
        $childHtml = $this->getChildHtml();

        return $formHtml . $childHtml;
    }
}
