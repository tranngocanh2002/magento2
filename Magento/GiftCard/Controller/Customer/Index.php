<?php
namespace Magento\GiftCard\Controller\Customer;

use Magento\Framework\App\Config\ScopeConfigInterface;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        ScopeConfigInterface $scopeConfig,
    )
    {
//        dd($this);
        $this->_pageFactory = $pageFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $giftCardEnabled = $this->scopeConfig->getValue('giftcard_section_id/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($giftCardEnabled != 1) {
            $this->_forward('noroute');
            return;
        }
        return $this->_pageFactory->create();
    }
}

