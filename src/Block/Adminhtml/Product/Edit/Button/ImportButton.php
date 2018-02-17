<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Block\Adminhtml\Product\Edit\Button;

class ImportButton extends AbstractButton
{
    /**
     * Gets the action label.
     *
     * @return string
     */
    protected function getActionLabel()
    {
        return 'Import';
    }

    /**
     * Gets the action URL.
     *
     * @return string
     */
    protected function getActionUrl()
    {
        return $this->getUrl('product_maintenance/product/import', ['product_id' => $this->getProductId()]);
    }
}