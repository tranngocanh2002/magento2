<?php
namespace Magento\GiftCard\Controller\Adminhtml\Gift;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\GiftCard\Model\GiftCardFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\GiftCard\Model\ResourceModel\GiftCard as GiftCardResource;

class MassDelete extends Action implements HttpPostActionInterface
{
    protected $giftCardFactory;
    protected $resource;
    protected $giftCardResource;
    protected $customerRepository;
    protected $eventManager;

    public function __construct(
        Context $context,
        GiftCardFactory $giftCardFactory,
        ResourceConnection $resource,
        GiftCardResource $giftCardResource,
        CustomerRepositoryInterface $customerRepository,
        ManagerInterface $eventManager,
    ) {
        parent::__construct($context);
        $this->giftCardFactory = $giftCardFactory;
        $this->resource = $resource;
        $this->giftCardResource = $giftCardResource;
        $this->customerRepository = $customerRepository;
        $this->eventManager = $eventManager;
    }

    public function execute()
    {
        $ids = $this->getRequest()->getParam('selected');
        if (!is_array($ids) || empty($ids)) {
            $all = $this->getRequest()->getParam('excluded');
            if (isset($all)) {
                $connection = $this->resource->getConnection();
                $tableName = $this->giftCardResource->getMainTable();
                $connection->beginTransaction();
                $connection->delete($tableName);
                $connection->commit();
                $this->messageManager->addSuccessMessage(__('All records from the Gift Card table have been deleted.'));
            }
        } else {
            try {
                foreach ($ids as $id) {
                    $giftCard = $this->giftCardFactory->create();
                    $giftCard->load($id);
                    $giftCard->delete();
                    if (is_numeric($giftCard->getOrigData('create_from'))) {
                    $customer = $this->customerRepository->getById($giftCard->getOrigData('create_from'));
                    $customerEmail = $customer->getEmail();
                        if (filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                            $this->eventManager->dispatch('custom_send_email_event', [
                                'title' => 'DELETE GIFT CARD',
                                'code' => $giftCard->getData('code'),
                                'balance' => $giftCard->getData('balance'),
                                'email' => $customerEmail,
                            ]);
                        }
                    }
                }
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('An error occurred while deleting records.'));
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/index');
    }
}
