<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
   ComponentRegistrar::MODULE,
   'Rubic_ProductMaintenance',
   __DIR__ . '/src'
);