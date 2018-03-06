<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Plugin;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\CatalogImportExport\Model\Export\Product as ExportProduct;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\Import;
use Rubic\ProductMaintenance\Helper\Reflection as ReflectionHelper;
use Rubic\ProductMaintenance\Helper\Config as ConfigHelper;

class SeparateAttributeColumnsPlugin
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ReflectionHelper
     */
    private $reflectionHelper;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ReflectionHelper $reflectionHelper
     * @param ConfigHelper $configHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ReflectionHelper $reflectionHelper,
        ConfigHelper $configHelper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->reflectionHelper = $reflectionHelper;
        $this->scopeConfig = $scopeConfig;
        $this->configHelper = $configHelper;
    }

    /**
     * Returns an array of user defined product attribute codes.
     *
     * @return array
     */
    private function getUserDefinedProductAttributeCodes()
    {
        $attributes = $this->attributeRepository->getList(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $this->searchCriteriaBuilder->addFilter('is_user_defined', 1)->create()
        );
        $attributeCodes = [];
        foreach ($attributes->getItems() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
    }

    /**
     * Removes additional_attributes from the header columns.
     *
     * @param ExportProduct $product
     * @param array $result
     * @return array
     */
    public function after_getHeaderColumns(ExportProduct $product, array $result)
    {
        if ($this->configHelper->shouldUseSeparateAttributeColumns()) {
            unset($result[array_search('additional_attributes', $result)]);
        }
        return $result;
    }

    /**
     * Change the delimiter for importing.
     *
     * @param ImportProduct $product
     * @param array $values
     * @param string $delimiter
     * @return array
     */
    public function beforeParseMultiselectValues(
        ImportProduct $product,
        $values,
        $delimiter = ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR
    ) {
        return [
            $values,
            $this->configHelper->shouldUseSeparateAttributeColumns() ?
                Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR : $delimiter
        ];
    }

    /**
     * Adds user defined product attributes to the main attribute codes.
     * This gives these attributes an individual column instead of being stuffed into additional_attributes.
     *
     * @param ExportProduct $product
     */
    public function beforeExport(ExportProduct $product)
    {
        if ($this->configHelper->shouldUseSeparateAttributeColumns()) {
            $userDefinedAttributeCodes = $this->getUserDefinedProductAttributeCodes();
            $mainAttributesProperty = $this->reflectionHelper
                ->getAccessibleObjectProperty($product, '_exportMainAttrCodes');
            $mainAttributeCodes = $mainAttributesProperty->getValue($product);
            $mainAttributeCodes = array_merge(
                $mainAttributeCodes,
                array_diff($userDefinedAttributeCodes, $mainAttributeCodes)
            );
            $mainAttributesProperty->setValue($product, $mainAttributeCodes);
        }
    }
}