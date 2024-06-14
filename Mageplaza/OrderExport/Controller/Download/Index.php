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

namespace Mageplaza\OrderExport\Controller\Download;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;

/**
 * Class Index
 * @package Mageplaza\OrderExport\Controller\Download
 */
class Index extends Action
{
    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $forwardFactory
     * @param ProfileFactory $profileFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ForwardFactory $forwardFactory,
        ProfileFactory $profileFactory,
        PageFactory $resultPageFactory
    ) {
        $this->forwardFactory = $forwardFactory;
        $this->profileFactory = $profileFactory;
        $this->fileFactory = $fileFactory;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $forward = $this->forwardFactory->create();
        $this->resultPageFactory->create();

        $id = $this->getRequest()->getParam('id');

        /** @var Profile $profile */
        $profile = $this->profileFactory->create()->load($id);
        $secretKey = $this->getRequest()->getParam('secretkey');
        if (!$profile->getId()) {
            $this->messageManager->addErrorMessage(__('Profile does not exits'));

            return $forward->forward('noroute');
        }
        if ($profile->getSecretKey() !== $secretKey) {
            $this->messageManager->addErrorMessage(__('Wrong key'));

            return $forward->forward('noroute');
        }
        $lastGeneratedFile = $profile->getLastGeneratedFile();

        try {
            return $this->fileFactory->create(
                $lastGeneratedFile,
                ['type' => 'filename', 'value' => 'mageplaza/order_export/profile/' . $lastGeneratedFile],
                'media'
            );
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something wrong when download file. %1', $e->getMessage()));

            return $forward->forward('noroute');
        }
    }
}
