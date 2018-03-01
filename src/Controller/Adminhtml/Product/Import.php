<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Controller\Adminhtml\Product;

use FireGento\FastSimpleImport\Model\Importer;
use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use League\Csv\Reader as CsvReader;
use Magento\ImportExport\Model\Import as ImportModel;

class Import extends AbstractAction
{
    /**
     * @var Importer
     */
    private $importer;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param Action\Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Importer $importer
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Action\Context $context,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        Importer $importer,
        DirectoryList $directoryList
    ) {
        parent::__construct($context, $productRepository, $scopeConfig);
        $this->importer = $importer;
        $this->directoryList = $directoryList;
    }

    /**
     * Reads CSV from path and returns associative array.
     *
     * @param string $path
     * @return array
     */
    private function readCsv($path)
    {
        $csv = CsvReader::createFromPath($path);
        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
        return iterator_to_array($csv->getRecords(), false);
    }

    /**
     * Gets the image import directory.
     *
     * @return string
     */
    private function getImageImportDirectory()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) .
            DIRECTORY_SEPARATOR .
            'import' .
            DIRECTORY_SEPARATOR;
    }

    /**
     * Moves and gets additional images.
     *
     * @param string $sku
     * @return array
     */
    private function getAdditionalImages($sku)
    {
        $additionalImages = [];
        $images = glob($this->getProductDirectory($sku) . 'images/*');
        foreach ($images as $image) {
            $fileParts = explode('/', $image);
            $fileName = $sku . '_' . end($fileParts);
            copy($image, $this->getImageImportDirectory() . $fileName);
            $additionalImages[] = $fileName;
        }
        return $additionalImages;
    }

    /**
     * Imports product.
     *
     * @return ResultInterface|ResponseInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $productId = $this->_request->getParam('product_id');
        /** @var Product $product */
        $product = $this->getProductById($productId);
        $sku = $product->getSku();
        $data = $this->readCsv($this->getProductCsvFile($sku));
        $data[0]['additional_images'] = implode(
            ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
            $this->getAdditionalImages($sku)
        );
        try {
            $this->importer->processImport($data);
            $this->messageManager->addSuccessMessage($this->importer->getLogTrace());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

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
