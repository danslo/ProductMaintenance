<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const XML_PATH_SEPARATE_ATTRIBUTE_COLUMNS = 'product_maintenance/general/separate_attribute_columns';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Determines if we should use separate attribute columns.
     *
     * @return bool
     */
    public function shouldUseSeparateAttributeColumns()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_SEPARATE_ATTRIBUTE_COLUMNS);
    }
}