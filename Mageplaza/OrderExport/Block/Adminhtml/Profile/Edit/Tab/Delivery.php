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
use Magento\Config\Model\Config\Source\Email\Identity as EmailIdentity;
use Magento\Config\Model\Config\Source\Email\Template as EmailTemplate;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\OrderExport\Model\Config\Source\Protocol;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class Delivery
 * @package Mageplaza\OrderExport\Block\Adminhtml\Profile\Edit\Tab
 */
class Delivery extends Generic implements TabInterface
{
    /**
     * @var Yesno
     */
    protected $yesno;

    /**
     * @var Protocol
     */
    protected $protocol;

    /**
     * @var EmailTemplate
     */
    protected $emailTemplate;

    /**
     * @var EmailIdentity
     */
    protected $emailIdentity;

    /**
     * Delivery constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param EmailTemplate $emailTemplate
     * @param EmailIdentity $emailIdentity
     * @param Yesno $yesno
     * @param Protocol $protocol
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        EmailTemplate $emailTemplate,
        EmailIdentity $emailIdentity,
        Yesno $yesno,
        Protocol $protocol,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->yesno         = $yesno;
        $this->protocol      = $protocol;
        $this->emailTemplate = $emailTemplate;
        $this->emailIdentity = $emailIdentity;
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

        $deliveryFieldset = $form->addFieldset('delivery_fieldset', [
            'legend' => __('Upload to a remote server'),
            'class'  => 'fieldset-wide'
        ]);
        $deliveryEnable   = $deliveryFieldset->addField('upload_enable', 'select', [
            'name'   => 'upload_enable',
            'label'  => __('Enable'),
            'title'  => __('Enable'),
            'values' => $this->yesno->toOptionArray()
        ]);
        $protocol         = $deliveryFieldset->addField('upload_type', 'select', [
            'name'   => 'upload_type',
            'label'  => __('Type'),
            'title'  => __('Type'),
            'values' => $this->protocol->toOptionArray()
        ]);
        $passiveMode      = $deliveryFieldset->addField('passive_mode', 'select', [
            'name'   => 'passive_mode',
            'label'  => __('Passive Mode'),
            'title'  => __('Passive Mode'),
            'values' => $this->yesno->toOptionArray()
        ]);
        $hostName         = $deliveryFieldset->addField('host_name', 'text', [
            'name'  => 'host_name',
            'label' => __('Hostname'),
            'title' => __('Hostname'),
            'note'  => __('It can be IP address or hostname. You can add port at the end of hostname. E.g. sftp.domain.com:22. No need add port with FTP type'),
        ]);
        $userName         = $deliveryFieldset->addField('user_name', 'text', [
            'name'  => 'user_name',
            'label' => __('User Name'),
            'title' => __('User Name'),
        ]);
        $password         = $deliveryFieldset->addField('password', 'password', [
            'name'  => 'password',
            'label' => __('Password'),
            'title' => __('Password'),
        ]);
        $directory        = $deliveryFieldset->addField('directory_path', 'text', [
            'name'  => 'directory_path',
            'label' => __('Directory Path'),
            'title' => __('Directory Path'),
            'note'  => __('Full path of a directory. E.g: /var/www/path/to/your-folder/'),
        ]);
        $checkConnect     = $deliveryFieldset->addField('check_connect', 'button', [
            'name'               => 'check_connect',
            'value'              => __('Check Connection'),
            'after_element_html' => '<div class="check-connect-message"></div>',
            'class'              => 'btn primary'
        ]);
        $emailFieldset    = $form->addFieldset('email_fieldset', [
            'legend' => __('Email'),
            'class'  => 'fieldset-wide'
        ]);
        $emailEnable      = $emailFieldset->addField('email_enable', 'select', [
            'name'   => 'email_enable',
            'label'  => __('Enable'),
            'title'  => __('Enable'),
            'values' => $this->yesno->toOptionArray()
        ]);
        $emailSender      = $emailFieldset->addField('sender', 'select', [
            'name'   => 'sender',
            'label'  => __('Sender'),
            'title'  => __('Sender'),
            'values' => $this->emailIdentity->toOptionArray()
        ]);
        $emailSubject     = $emailFieldset->addField('email_subject', 'text', [
            'name'  => 'email_subject',
            'label' => __('Email Subject'),
            'title' => __('Email Subject'),
        ]);
        $emailTo          = $emailFieldset->addField('send_email_to', 'text', [
            'name'  => 'send_email_to',
            'label' => __('Send Email To'),
            'title' => __('Send Email To'),
            'note'  => __(
                'Separated by commas ",".
                An email with an exported file attachment will be sent to this address after it is generated.
                Compatible with Mageplaza <a href="https://www.mageplaza.com/magento-2-smtp/" target="_blank">SMTP</a>'
            ),
        ]);
        $emailTemplate    = $emailFieldset->addField('email_template', 'select', [
            'name'   => 'email_template',
            'label'  => __('Email Template'),
            'title'  => __('Email Template'),
            'values' => $this->emailTemplate->setPath('mp_order_export/send_file_email_template')->toOptionArray(),
        ]);
        $httpFieldset     = $form->addFieldset('http_fieldset', [
            'legend' => __('HTTP Request'),
            'class'  => 'fieldset-wide'
        ]);
        $httpRequest      = $httpFieldset->addField('http_enable', 'select', [
            'name'   => 'http_enable',
            'label'  => __('Enable'),
            'title'  => __('Enable'),
            'values' => $this->yesno->toOptionArray()
        ]);
        $httpUrl          = $httpFieldset->addField('http_url', 'text', [
            'name'  => 'http_url',
            'label' => __('URL'),
            'title' => __('URL'),
        ]);
        $httpHeader       = $httpFieldset->addField('http_header', 'textarea', [
            'name'  => 'http_header',
            'label' => __('Headers'),
            'title' => __('Headers'),
            'note'  => __('Rows separated by line breaks, Keys and Values separated by colon ":". For example: : <br>
            Content-Type: javascript/json <br>
            Content_length: 200
            ')
        ]);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Dependence::class)
                ->addFieldMap($protocol->getHtmlId(), $protocol->getName())
                ->addFieldMap($passiveMode->getHtmlId(), $passiveMode->getName())
                ->addFieldMap($hostName->getHtmlId(), $hostName->getName())
                ->addFieldMap($userName->getHtmlId(), $userName->getName())
                ->addFieldMap($directory->getHtmlId(), $directory->getName())
                ->addFieldMap($password->getHtmlId(), $password->getName())
                ->addFieldMap($deliveryEnable->getHtmlId(), $deliveryEnable->getName())
                ->addFieldMap($checkConnect->getHtmlId(), $checkConnect->getName())
                ->addFieldMap($emailEnable->getHtmlId(), $emailEnable->getName())
                ->addFieldMap($emailSender->getHtmlId(), $emailSender->getName())
                ->addFieldMap($emailSubject->getHtmlId(), $emailSubject->getName())
                ->addFieldMap($emailTo->getHtmlId(), $emailTo->getName())
                ->addFieldMap($emailTemplate->getHtmlId(), $emailTemplate->getName())
                ->addFieldMap($httpRequest->getHtmlId(), $httpRequest->getName())
                ->addFieldMap($httpHeader->getHtmlId(), $httpHeader->getName())
                ->addFieldMap($httpUrl->getHtmlId(), $httpUrl->getName())
                ->addFieldDependence($protocol->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($passiveMode->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($passiveMode->getName(), $protocol->getName(), 'ftp')
                ->addFieldDependence($hostName->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($userName->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($password->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($directory->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($checkConnect->getName(), $deliveryEnable->getName(), '1')
                ->addFieldDependence($emailSender->getName(), $emailEnable->getName(), '1')
                ->addFieldDependence($emailSubject->getName(), $emailEnable->getName(), '1')
                ->addFieldDependence($emailTo->getName(), $emailEnable->getName(), '1')
                ->addFieldDependence($emailTemplate->getName(), $emailEnable->getName(), '1')
                ->addFieldDependence($httpHeader->getName(), $httpRequest->getName(), '1')
                ->addFieldDependence($httpUrl->getName(), $httpRequest->getName(), '1')
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
        return __('Delivery');
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
}
