<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ImportExport\Model\Export as ExportModel;
use Magento\ImportExport\Model\ExportFactory as ExportFactory;

class Export extends AbstractAction
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var ExportFactory
     */
    private $exportFactory;

    /**
     * @param Action\Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductResource $productResource
     * @param ExportFactory $exportFactory
     */
    public function __construct(
        Action\Context $context,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        ProductResource $productResource,
        ExportFactory $exportFactory
    ) {
        parent::__construct($context, $productRepository, $scopeConfig);
        $this->productResource = $productResource;
        $this->exportFactory = $exportFactory;
    }

    /**
     * Writes product data to CSV.
     *
     * @param string $sku
     * @param string $data
     * @return void
     */
    private function writeProductData($sku, $data)
    {
        $productDirectory = $this->getProductDirectory($sku);
        @mkdir($productDirectory . 'images', 0777, true);
        file_put_contents($this->getProductCsvFile($sku), $data);
    }

    /**
     * Gets product export data by SKU.
     *
     * @param string $sku
     * @return string
     * @throws LocalizedException
     */
    private function getProductExportData($sku)
    {
        $exporter = $this->exportFactory->create([
            'data' => [
                ExportModel::FILTER_ELEMENT_GROUP => ['sku' => $sku],
                'strict_sku_filter' => true,
                'entity' => 'catalog_product',
                'file_format' => 'csv'
            ]
        ]);
        return $exporter->export();
    }

    /**
     * Exports product to CSV.
     *
     * @return ResultInterface|ResponseInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $productId = $this->_request->getParam('product_id');
        /** @var Product $product */
        $product = $this->getProductById($productId);
        $exportData = $this->getProductExportData($product->getSku());
        $this->writeProductData($product->getSku(), $exportData);

        $this->messageManager->addSuccessMessage('Exported product.');
        $redirect = $this->resultRedirectFactory->create();

        $returnPath = self::CATALOG_PRODUCT_EDIT_PATH;
        $params = ['id' => $productId];

        if ($this->_request->getParam('toGrid')) {
            $returnPath = self::CATALOG_PRODUCT_GRID_PATH;
            $params = [];
        }

        $redirect->setPath($returnPath, $params);
        return $redirect;
    }
}