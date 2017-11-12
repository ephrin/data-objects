<?php

namespace Ephrin\DataObject\PropertyDriver;

use Ephrin\DataObject\Type\Immutable;
use Ephrin\DataObject\MetaReader;
use Ephrin\DataObject\PropertyMeta;
use phpDocumentor\Reflection\DocBlock\Tags as phpDoc;
use phpDocumentor\Reflection\DocBlockFactory;

class DocPropertyMetaReader implements MetaReader
{
    /**
     * @var PropertyMeta[][]
     */
    static protected $metaCache = [];

    /**
     * @param object $instance
     * @param array $defaults
     * @return \Ephrin\DataObject\PropertyMeta[]
     * @throws \ReflectionException
     * @throws \LogicException
     */
    public function readMeta($instance, array $defaults = [])
    {
        $class = get_class($instance);

        if (isset(self::$metaCache[$class])) {
            return self::$metaCache[$class];
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

                $meta = new PropertyMeta();
                $meta->name = $propertyName;
                $meta->type = $type;

                if ($instance instanceof Immutable){
                    if($item instanceof phpDoc\PropertyWrite) {
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
                    $meta->writable = false;
                } else {
                    $meta->writable = $item instanceof phpDoc\Property || !$item instanceof phpDoc\PropertyRead;
                }

                $meta->readable = !$item instanceof phpDoc\PropertyWrite;

                $metas[$propertyName] = $meta;
            }
        }

        return self::$metaCache[$class] = $metas;
    }
}
