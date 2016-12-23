<?php

namespace Ephrin\Immutable;

use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite;
use phpDocumentor\Reflection\DocBlockFactory;

class StructureFactory
{
    private static $annotations = [];

    public static function create($object)
    {
        //todo cache

        $class = get_class($object);

        if (!isset(self::$annotations[$class])) {
            self::$annotations[$class] = self::readStructureOpts($class);
        }

        return new DocStructure($class, self::$annotations[$class]);
    }

    /**
     * @param string $class
     * @return array
     */
    private static function readStructureOpts($class)
    {
        $factory = DocBlockFactory::createInstance();
        $reflection = new \ReflectionClass($class);
        $comment = $reflection->getDocComment();
        $c = $factory->create($comment);
        $properties = [];
        foreach (['property' => true, 'property-write' => true, 'property-read' => false] as $tag => $writable) {
            foreach ($c->getTagsByName($tag) as $item) {
                /** @var Property|PropertyWrite|PropertyRead $item */
                $type = strtolower($item->getType());
                $properties[$item->getVariableName()] = new PropertyType(
                    $type,
                    self::validationCallback($type),
                    $item instanceof Property || $item instanceof PropertyWrite
                );
            }
        }

        return $properties;
    }

    private static function validationCallback($type)
    {
        if (function_exists($fun = 'is_' . $type)) {
            return $fun;
        }

        if (class_exists($type, true)) {
            return function ($value) use ($type) {
                return $value instanceof $type;
            };
        }

        if ($type === 'mixed') {
            return function () {
                return true;
            };
        }

        throw new \InvalidArgumentException(
            sprintf('Unknown test for type `%s`.' .
                ' Supported are those one that can be tested with is_xxx function, mixed and object instances.', $type)
        );
    }

    private static function castCallback($type, $value)
    {
        if(class_exists($type)) {
            if(!$value instanceof $type) {
                throw new \InvalidArgumentException('type miss');
            }
        } else {
            settype($value, $type);
        }
        return $value;
    }
}
