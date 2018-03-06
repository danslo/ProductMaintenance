<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Plugin;

use Magento\CatalogImportExport\Model\Export\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Rubic\ProductMaintenance\Helper\Reflection as ReflectionHelper;

class SetStrictSkuFilterPlugin
{
    /**
     * @var ReflectionHelper
     */
    private $reflectionHelper;

    /**
     * @param ReflectionHelper $reflectionHelper
     */
    public function __construct(ReflectionHelper $reflectionHelper)
    {
        $this->reflectionHelper = $reflectionHelper;
    }

    /**
     * Magento 2 product exports treats all filterable attributes it thinks came from user input
     * as a 'FILTER_TYPE_INPUT', in which case it will filter using a LIKE %...%.
     *
     * However, any attribute that has its own source model or has 'filter_options' data set, will
     * get a stricter 'eq' filter.
     *
     * It's necessary for us to do strict filtering, because we only want to export a single product
     * based on the SKU.
     *
     * @param Product $product
     * @param Collection $collection
     * @return Collection
     */
    public function afterFilterAttributeCollection(Product $product, Collection $collection)
    {
        $parameters = $this->reflectionHelper->getAccessibleObjectProperty($product, '_parameters')->getValue($product);
        if ($parameters['strict_sku_filter'] ?? false) {
            $skuAttribute = $collection->getItemByColumnValue('attribute_code', 'sku');
            $skuAttribute->setFilterOptions(true);
        }
        return $collection;
    }
}