<?php

namespace Ephrin\Immutable;

use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite;
use phpDocumentor\Reflection\DocBlockFactory;

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
            self::$annotations[$class] = self::readProperties($class);
        }

        return new DocStructure($class, self::$annotations[$class], $defaults);
    }

    /**
     * @param string $class
     * @return array
     */
    private static function readProperties($class)
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
                $properties[$item->getVariableName()] = new DocProperty(
                    $type,
                    $item instanceof Property || $item instanceof PropertyWrite,
                    self::valueGateCallback($type, $class)
                );
            }
        }

        return $properties;
    }

    private static function valueGateCallback($type, $class)
    {
        //todo find out cb
        static $nv;

        if (!$nv) {
            $nv = function ($value) {
                return $value;
            };
        }

        return $nv;
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

        if (class_exists($type, true)) {
            return function ($value) use ($type) {
                return $value instanceof $type;
            };
        }

        throw new \InvalidArgumentException(
            sprintf('Unknown test for type `%s`.' .
                ' Supported are those one that can be tested with is_xxx function, mixed and object instances.', $type)
        );
    }
}
