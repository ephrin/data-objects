<?php

namespace Ephrin\DataObject;

use Ephrin\DataObject\Type\Strict;

class DefaultGateFactory
{
    /**
     * @param string $type
     * @param string $class
     * @param string $propertyName
     * @return \Closure
     * @throws \InvalidArgumentException
     */
    public static function create($type, $class, $propertyName)
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
