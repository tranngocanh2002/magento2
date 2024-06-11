<?php
namespace Magento\GiftCard\Controller\Adminhtml\Gift;

class NewAction extends \Magento\Backend\App\Action
{
    protected $_resultForwardFactory = false;
    public function __construct
    (
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
//        die(__METHOD__);
    }

    public function execute()
    {
//        die(__METHOD__);
        $resultPage = $this->_resultForwardFactory->create();
        $resultPage->forward('edit');
        return $resultPage;
    }
}
