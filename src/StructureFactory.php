<?php

namespace Ephrin\DataObject;

class StructureFactory
{
    /**
     * @param MetaReader $driver
     * @param object $object
     * @param array $defaults
     * @return Structure
     * @throws \InvalidArgumentException
     */
    public static function create(MetaReader $driver, $object, array $defaults = [])
    {
        $class = get_class($object);
        $properties = [];
        foreach ($driver->readMeta($object) as $meta) {
            $property = new Property(
                $meta,
                array_key_exists($meta->name, $defaults) ? $meta->pass($defaults[$meta->name]) : null
            );

            $properties[$meta->name] = $property;
        }

        return new Structure($class, $properties, $defaults);
    }
}
