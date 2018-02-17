<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;

abstract class AbstractButton extends Generic
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     */
    public function __construct(Context $context, Registry $registry, RequestInterface $request)
    {
        parent::__construct($context, $registry);
        $this->request = $request;
    }

    /**
     * Gets the action label.
     *
     * @return string
     */
    abstract protected function getActionLabel();

    /**
     * Gets the action URL.
     *
     * @return string
     */
    abstract protected function getActionUrl();

    /**
     * Gets the current product ID.
     *
     * @return int
     */
    protected function getProductId()
    {
        return $this->request->getParam('id');
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'      => __($this->getActionLabel()),
            'on_click'   => "setLocation('{$this->getActionUrl()}')",
            'sort_order' => 100
        ];
    }
}