<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Plugin;

use Magento\Catalog\Ui\Component\Listing\Columns\ProductActions;
use Magento\Framework\UrlInterface;

class AddMaintenanceActionsPlugin
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
    }

    /**
     * Add import and export buttons to product actions in the grid.
     *
     * @param ProductActions $productActions
     * @param array $dataSource
     * @return array
     */
    public function afterPrepareDataSource(ProductActions $productActions, array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                foreach (['export', 'import'] as $actionType) {
                    $item[$productActions->getData('name')][$actionType] = [
                        'href' => $this->url->getUrl(
                            sprintf('product_maintenance/product/%s', $actionType),
                            [
                                'product_id' => $item['entity_id'],
                                'toGrid' => true
                            ]
                        ),
                        'label' => __(ucfirst($actionType)),
                        'hidden' => false,
                    ];
                }
            }
        }
        return $dataSource;
    }
}