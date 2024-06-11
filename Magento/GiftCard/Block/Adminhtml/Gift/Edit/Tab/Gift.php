<?php
namespace Magento\GiftCard\Block\Adminhtml\Gift\Edit\Tab;
//use Magento\GiftCard\Model\GiftCard;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Gift extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $giftCardFactory;
    protected $scopeConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\GiftCard\Model\Gift $giftCardFactory,
        ScopeConfigInterface $scopeConfig,
    )
    {
        parent::__construct($context, $registry, $formFactory);
        $this->giftCardFactory = $giftCardFactory;
        $this->scopeConfig = $scopeConfig;
    }

    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $giftCard = $this->giftCardFactory->load($id)->getStoredData();
        $code_length = $this->scopeConfig->getValue('giftcard_section_id/general_test/code_length', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
//        dd($this->scopeConfig->getValue('giftcard_section_id/general_test/code_length'));
        $form = $this->_formFactory->create();
        if ($this->_request->getParam('id')) {
            $balance = isset($giftCard['balance']) ? intval($giftCard['balance']) : 0.0;
//            dd($balance);
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card information')]);
            $fieldset->addField('code', 'text', [
                'name'     => 'code',
                'label'    => __('Code'),
                'title'    => __('Template Name'),
                'required' => true,
                'readonly' => true,
                'value'    => $giftCard['code']
            ]);
            $fieldset->addField('from', 'text', [
                'name'     => 'from',
                'label'    => __('Create From'),
                'title'    => __('Template Name'),
                'required' => true,
                'readonly' => true,
                'value'    => $giftCard['time_occurred']
            ]);
            $fieldset->addField('balance', 'text', [
                'name' => 'balance',
                'label' => __('Balance'),
                'title' => __('Balance'),
                'required' => true,
                'value'    => $balance,
                'class' => 'validate-number validate-greater-than-zero'
            ]);
            $fieldset->addField('id', 'hidden', [
                'name' => 'id',
                'value' => $this->_request->getParam('id')
            ]);
        }
        else {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card information')]);
            $fieldset->addField('length', 'text', [
                'name' => 'length',
                'label' => __('Code length'),
                'title' => __('Template Name'),
                'required' => true,
                'class' => 'validate-number validate-greater-than-zero',
                'value' => $code_length
            ]);
            $fieldset->addField('balance', 'text', [
                'name' => 'balance',
                'label' => __('Balance'),
                'title' => __('Balance'),
                'required' => true,
                'class' => 'validate-number validate-greater-than-zero',
            ]);
        }




        $this->setForm($form);
        return parent::_prepareForm();
    }


    public function getTabLabel()
    {
        return __('Gift card information');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
