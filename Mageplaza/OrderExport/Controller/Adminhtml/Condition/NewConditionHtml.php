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

namespace Mageplaza\OrderExport\Controller\Adminhtml\Condition;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Mageplaza\OrderExport\Controller\Adminhtml\AbstractManageProfiles;
use Mageplaza\OrderExport\Model\Profile;

/**
 * Class NewConditionHtml
 * @package Mageplaza\OrderExport\Controller\Adminhtml\Condition
 */
class NewConditionHtml extends AbstractManageProfiles
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $id       = $this->getRequest()->getParam('id');
        $formName = $this->getRequest()->getParam('form_namespace');
        $typeArr  = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type     = $typeArr[0];
        $html     = '';

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_objectManager->create(Profile::class))
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($formName);
            $html = $model->asHtmlRecursive();
        }

        $this->getResponse()->setBody($html);
    }
}
