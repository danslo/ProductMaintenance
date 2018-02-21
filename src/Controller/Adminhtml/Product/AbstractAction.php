<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;

abstract class AbstractAction extends Action
{
    /**
     * Directory to export/import products to/from.
     */
    const XML_PATH_PRODUCT_DIRECTORY = 'product_maintenance/general/product_directory';

    /**
     * Path to product edit page
     */
    const CATALOG_PRODUCT_EDIT_PATH = 'catalog/product/edit';

    /**
     * Path to product grid
     */
    const CATALOG_PRODUCT_GRID_PATH = 'catalog/product/index';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Action\Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Action\Context $context,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Gets product by product id.
     *
     * @param int $productId
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    protected function getProductById($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * Gets the product directory from config.
     *
     * @param string $sku
     * @return string
     */
    protected function getProductDirectory($sku)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_DIRECTORY) .
            DIRECTORY_SEPARATOR .
            $sku .
            DIRECTORY_SEPARATOR;
    }

    /**
     * Gets path to product CSV file.
     *
     * @param string $sku
     * @return string
     */
    protected function getProductCsvFile($sku)
    {
        return $this->getProductDirectory($sku) . $sku . '.csv';
    }
}