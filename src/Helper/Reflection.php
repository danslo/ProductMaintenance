<?php
/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Rubic\ProductMaintenance\Helper;

class Reflection
{
    /**
     * Since Import/Export doesn't (yet) strictly follow composition over inheritance, we use reflection to get access
     * to certain protected properties inside our plugins.
     *
     * @param object $object
     * @param string $property
     * @return \ReflectionProperty
     */
    public function getAccessibleObjectProperty($object, $property)
    {
        $object = new \ReflectionObject($object);
        $property = $object->getProperty($property);
        $property->setAccessible(true);
        return $property;
    }
}