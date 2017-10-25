<?php

namespace Ephrin\Immutable;

use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite;
use phpDocumentor\Reflection\DocBlockFactory;
use SebastianBergmann\GlobalState\RuntimeException;

class DocStructureFactory
{
    /**
     * @var array
     */
    private static $annotations = [];

    public static function create($object, array $defaults = [])
    {
        $class = get_class($object);

        if (!isset(self::$annotations[$class])) {
            self::$annotations[$class] = self::readProperties($object, $class);
        }

        return new DocStructure($object, self::$annotations[$class], $defaults);
    }

    /**
     * @param object $instance
     * @param string $class
     * @return array
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private static function readProperties($instance, $class)
    {
        $factory = DocBlockFactory::createInstance();
        $reflection = new \ReflectionClass($class);
        $comment = $reflection->getDocComment();
        $docBlock = $factory->create($comment);
        $properties = [];
        foreach (['property' => true, 'property-write' => true, 'property-read' => false] as $tag => $writable) {
            foreach ($docBlock->getTagsByName($tag) as $item) {
                /** @var DocProperty|PropertyWrite|PropertyRead $item */
                $type = strtolower($item->getType());
                $propertyName = $item->getVariableName();
                if ($instance instanceof Immutable && $item instanceof PropertyWrite) {
                    throw new \LogicException(
                        sprintf(
                            'Mutable property `%s` of %s is declared (@%s) while object is immutable. ' .
                            'It is not possible to change state of immutable object as class implements %s.',
                            $propertyName,
                            get_class($instance),
                            $tag,
                            Immutable::class
                        )
                    );
                }
                $properties[$item->getVariableName()] = new DocProperty(
                    $type,
                    $item instanceof Property || $item instanceof PropertyWrite,
                    !$item instanceof PropertyWrite,
                    self::valueGateCallback($type, $instance, $propertyName)
                );
            }
        }

        return $properties;
    }

    /**
     * @param string $type
     * @param object $instance
     * @param string $propertyName
     * @return \Closure
     * @throws \InvalidArgumentException
     */
    private static function valueGateCallback($type, $instance, $propertyName)
    {
        $typeValidation = self::typeValidationCb($type);

        if ($instance instanceof Strict) {
            $typeValidation = function ($value) use ($typeValidation, $type, $instance, $propertyName) {
                if (!$typeValidation($value)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Unexpected type %s for property %s in %s. Type of %s is expected.',
                            is_object($value) ? get_class($value) : gettype($value),
                            $propertyName,
                            get_class($instance),
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
