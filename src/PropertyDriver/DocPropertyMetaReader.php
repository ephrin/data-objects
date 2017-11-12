<?php

namespace Ephrin\Immutable\PropertyDriver;

use Ephrin\Immutable\Immutable;
use Ephrin\Immutable\MetaReader;
use Ephrin\Immutable\PropertyMeta;
use phpDocumentor\Reflection\DocBlock\Tags as phpDoc;
use phpDocumentor\Reflection\DocBlockFactory;

class DocPropertyMetaReader implements MetaReader
{
    /**
     * @var PropertyMeta[][]
     */
    static protected $meta = [];

    /**
     * @param object $instance
     * @param array $defaults
     * @return \Ephrin\Immutable\PropertyMeta[]
     * @throws \ReflectionException
     * @throws \LogicException
     */
    public function readMeta($instance, array $defaults = [])
    {
        $class = get_class($instance);

        if (isset(self::$meta[$class])) {
            return self::$meta[$class];
        }

        $metas = [];

        $reflection = new \ReflectionClass($class);
        $comment = $reflection->getDocComment();
        $docBlock = DocBlockFactory::createInstance()->create($comment);

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
                $meta = new PropertyMeta();
                $meta->name = $propertyName;
                $meta->type = $type;
                $meta->readable = !$item instanceof phpDoc\PropertyWrite;
                $meta->writable = $item instanceof phpDoc\Property || !$item instanceof phpDoc\PropertyRead;
                $metas[$propertyName] = $meta;
            }
        }

        return self::$meta[$class] = $metas;
    }
}
