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

namespace Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\OrderExport\Model\Profile;
use Mageplaza\OrderExport\Model\ProfileFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\OrderExport\Controller\Adminhtml\ManageProfiles
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ProfileFactory $profileFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ProfileFactory $profileFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->profileFactory = $profileFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $profileItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($profileItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($profileItems);
        $profileId = !empty($key) ? (int)$key[0] : '';
        /** @var Profile $profile */
        $profile = $this->profileFactory->create()->load($profileId);
        try {
            $profileData = $profileItems[$profileId];
            $profile->addData($profileData);
            $profile->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithProfileId($profile, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithProfileId($profile, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithProfileId(
                $profile,
                __('Something went wrong while saving the Profile.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Profile id to error message
     *
     * @param Profile $profile
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithProfileId(Profile $profile, $errorText)
    {
        return '[Profile ID: ' . $profile->getId() . '] ' . $errorText;
    }
}
