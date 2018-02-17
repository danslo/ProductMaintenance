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
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

class Export extends AbstractAction
{
    const MULTIVALUE_SEPARATOR = '|';

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @param Action\Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductResource $productResource
     */
    public function __construct(
        Action\Context $context,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig,
        ProductResource $productResource
    ) {
        parent::__construct($context, $productRepository, $scopeConfig);
        $this->productResource = $productResource;
    }

    /**
     * Gets the data to export.
     *
     * @param Product $product
     * @return array
     */
    private function getExportData($product)
    {
        $exportData = ['sku' => $product->getSku(), 'product_websites' => 'base'];
        foreach ($product->getAttributes() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            try {
                /** @var AbstractFrontend $frontend */
                $frontend = $this->productResource->getAttribute($attributeCode)->setStoreId(0)->getFrontend();
                if ($product->getData($attributeCode) === null) {
                    $exportData[$attributeCode] = '';
                } elseif (in_array($attribute->getFrontendInput(), ['multiselect', 'select']) &&
                    $attribute->getBackend() instanceof ArrayBackend) {
                    $options = [];
                    foreach (explode(',', $product->getData($attributeCode)) as $option) {
                        $options[] = $frontend->getOption($option);
                    }
                    $exportData[$attributeCode] = count($options) ?
                        implode(self::MULTIVALUE_SEPARATOR, $options) : '';
                } else {
                    $value = $frontend->getValue($product);
                    if (is_scalar($value)) {
                        $exportData[$attributeCode] = $value;
                    } elseif ($value instanceof Phrase) {
                        $exportData[$attributeCode] = $value->getText();
                    } else {
                        $exportData[$attributeCode] = '';
                    }
                }
            } catch (\Exception $e) {}
        }
        $exportData['tax_class_name'] = $exportData['tax_class_id'];
        unset($exportData['tax_class_id']);
        return $exportData;
    }

    /**
     * Encodes data for csv output.
     *
     * @param string $value
     * @return string
     */
    private function encodeData($value)
    {
        $value = str_replace('\\"','"',$value);
        $value = str_replace('"','\"',$value);
        return '"'.$value.'"';
    }

    /**
     * Writes product data to CSV.
     *
     * @param string $sku
     * @param array $data
     * @return void
     */
    private function writeProductData($sku, $data)
    {
        $productDirectory = $this->getProductDirectory($sku);
        @mkdir($productDirectory . 'images', 0777, true);

        $handle = fopen($this->getProductCsvFile($sku), 'w');
        fputcsv($handle, array_keys($data));
        fputs($handle, implode(",", array_map([$this, 'encodeData'], $data))."\r\n");
        fclose($handle);
    }

    /**
     * Exports product to CSV.
     *
     * @return ResultInterface|ResponseInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $productId = $this->_request->getParam('product_id');
        /** @var Product $product */
        $product = $this->getProductById($productId);
        $exportData = $this->getExportData($product);
        $this->writeProductData($product->getSku(), $exportData);

        $this->messageManager->addSuccessMessage('Exported product.');
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('catalog/product/edit', ['id' => $productId]);
        return $redirect;
    }
}