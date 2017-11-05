<?php

namespace Ephrin\Immutable;

class StructureFactory
{
    /**
     * @var array
     */
    private static $annotations = [];

    /**
     * @param PropertyDriver $driver
     * @param object $object
     * @param array $defaults
     * @return Structure
     * @throws \InvalidArgumentException
     */
    public static function create(PropertyDriver $driver, $object, array $defaults = [])
    {
        $class = get_class($object);
        if (!isset(self::$annotations[$class])) {
            $properties = [];
            foreach ($driver->properties($object) as $property) {
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

    /**
     * @param string $type
     * @param string $class
     * @param string $propertyName
     * @return \Closure
     * @throws \InvalidArgumentException
     */
    private static function valueGateCallback($type, $class, $propertyName)
    {
        $typeValidation = self::typeValidationCb($type);

        if ($class instanceof Strict) {
            $typeValidation = function ($value) use ($typeValidation, $type, $class, $propertyName) {
                if (!$typeValidation($value)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Unexpected type %s for property %s in %s. Type of %s is expected.',
                            is_object($value) ? get_class($value) : gettype($value),
                            $propertyName,
                            $class,
                            $type
                        )
                    );
                }
            };
        }

        if (self::canCastType($type)) {
            return function ($value) use ($typeValidation, $type) {
                if (!$typeValidation($value)) {
                    settype($value, $type);
                }

                return $value;
            };
        }

        return function ($value) use ($typeValidation) {
            $typeValidation($value);

            return $value;
        };
    }

    private static function typeValidationCb($type)
    {
        if (function_exists($fun = 'is_' . $type)) {
            return $fun;
        }

        if ($type === 'mixed') {
            return function () {
                return true;
            };
        }

        if (class_exists($type)) {
            return function ($value) use ($type) {
                return $value instanceof $type;
            };
        }

        throw new \InvalidArgumentException(
            sprintf('Unknown test for type `%s`.' .
                ' Supported are those one that can be tested with is_xxx function, mixed and object instances.', $type)
        );
    }

    private static function canCastType($type)
    {
        return in_array(
            $type,
            ['bool', 'boolean', 'int', 'integer', 'string', 'float', 'double', 'array', 'object', 'null'],
            true
        );
    }
}
