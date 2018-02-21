<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ActionColumn extends Column
{

    /**
     * ActionColumn constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->context = $context;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $row = new DataObject($item);

        $url = $this->context->getUrl('product_maintenance/product/export',
            [
                'product_id' => $row->getEntityId(),
                'toGrid' => true
            ]
        );
        $output = sprintf('<a target="_blank" href="%s" data-url="%s" title="%s">%s</a>',
            $url,
            $url,
            __('Export'),
            __('Export')
        );

        $output .= ' | ';

        $url = $this->context->getUrl('product_maintenance/product/import',
            [
                'product_id' => $row->getEntityId(),
                'toGrid' => true
            ]
        );
        $output .= sprintf('<a target="_blank" href="%s" data-url="%s" title="%s">%s</a>',
            $url,
            $url,
            __('Import'),
            __('Import')
        );

        return $output;
    }
}
