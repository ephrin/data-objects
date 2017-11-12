<?php

namespace Ephrin\Immutable;

class StructureFactory
{
    /**
     * @var array
     */
    private static $annotations = [];

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
        if (!isset(self::$annotations[$class])) {
            $properties = [];
            foreach ($driver->readMeta($object) as $property) {
                $property->valueGate = self::valueGateCallback($property->type, get_class($object), $property->name);

                if (isset($defaults[$property->name])) {
                    $property->defaultValue = call_user_func($property->valueGate, $defaults[$property->name]);
                }

                $properties[$property->name] = $property;
            }
            self::$annotations[$class] = $properties;
        }

        return new Structure($class, self::$annotations[$class], $defaults);
    }


}
