<?php

namespace Ephrin\Immutable\PropertyDriver;

use Ephrin\Immutable\Immutable;
use Ephrin\Immutable\Property;
use Ephrin\Immutable\PropertyDriver;
use phpDocumentor\Reflection\DocBlock\Tags as phpDoc;
use phpDocumentor\Reflection\DocBlockFactory;

class DocPropertyDriver implements PropertyDriver
{
    public function properties($instance, array $defaults = [])
    {
        $class = get_class($instance);
        $reflection = new \ReflectionClass($class);
        $comment = $reflection->getDocComment();
        $docBlock = DocBlockFactory::createInstance()->create($comment);
        $properties = [];
        foreach (['property' => true, 'property-write' => true, 'property-read' => false] as $tag => $writable) {
            foreach ($docBlock->getTagsByName($tag) as $item) {
                /** @var phpDoc\Property|phpDoc\PropertyWrite|phpDoc\PropertyRead $item */
                $type = strtolower($item->getType());
                $propertyName = $item->getVariableName();
                if ($instance instanceof Immutable && $item instanceof phpDoc\PropertyWrite) {
                    throw new \LogicException(
                        sprintf(
                            'Mutable property `%s` of %s is declared (@%s) while object is immutable. ' .
                            'It is not possible to change state of immutable object as class implements %s. ',
                            $propertyName,
                            get_class($instance),
                            $tag,
                            Immutable::class
                        )
                    );
                }
                $property = new Property;
                $property->name = $propertyName;
                $property->type = $type;
                $property->readable = !$item instanceof phpDoc\PropertyWrite;
                $property->writable = $item instanceof phpDoc\Property || !$item instanceof phpDoc\PropertyRead;

                yield $property;
            }
        }

        return $properties;
    }
}
