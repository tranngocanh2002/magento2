<?php
namespace Magento\GiftCard\Controller\Adminhtml\Gift;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\GiftCard\Model\GiftCardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;


class Save extends Action
{
    protected $giftCardFactory;
    protected $customerRepository;
    protected $eventManager;


    public function __construct(
        Context $context,
        GiftCardFactory $giftCardFactory,
        ManagerInterface $eventManager,
        CustomerRepositoryInterface $customerRepository,
    ) {
        parent::__construct($context);
        $this->giftCardFactory = $giftCardFactory;
        $this->eventManager = $eventManager;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $url = $this->getRequest()->getRequestUri();
        $lastSegment = basename($url);
//        dd($lastSegment);

        if ($data) {
            $balance = isset($data['balance']) ? (float)$data['balance'] : 0.0;
            if (isset($data['length'])) {
                $code = $this->generateRandomCode($data['length']);

                $giftCard = $this->giftCardFactory->create();
                $giftCard->setData([
                    'code' => $code,
                    'balance' => $balance,
                    'amount_used' => $balance,
                    'create_from' => $this->_auth->getUser()->getUsername()
                ]);

                try {
                    $giftCard->save();
                    $this->messageManager->addSuccessMessage(__('Gift card was successfully saved.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }

                if ($lastSegment == 'save'){
                    return $this->_redirect('*/*/index');
                } else {
                    return $this->_redirect('*/*/edit', ['id' => $giftCard->getId()]);
                }

            } else {
                $giftCard = $this->giftCardFactory->create()->load($data['id']);
                $giftCard->addData([
                    'code' => $data['code'],
//                    'balance' => $balance,
                    'amount_used' => $balance,
//                    'create_from' => $this->_auth->getUser()->getUsername()
                ]);

                try {
                    if (is_numeric($giftCard->getOrigData('create_from'))) {
                        $customer = $this->customerRepository->getById($giftCard->getOrigData('create_from'));
                        $customerEmail = $customer->getEmail();
                        if (filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                            $this->eventManager->dispatch('custom_send_email_event', [
                                'title' => 'EDIT GIFT CARD',
                                'code' => $data['code'],
                                'balance' => $balance,
                                'email' => $customerEmail,
                            ]);
                        }
                    }

                    $giftCard->save();
                    $this->messageManager->addSuccessMessage(__('Gift card was successfully edit.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $giftCard->getId()]);
                }

                return $this->_redirect('*/*/index');

            }
        }

//        $this->messageManager->addErrorMessage(__('No data to save.'));
//        return $this->_redirect('*/*/index');
    }

    private function generateRandomCode($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        if (!is_numeric($length) || $length > 36 ) {
            $length = 36;
        }
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
