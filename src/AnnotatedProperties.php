<?php

namespace Ephrin\Immutable;

class AnnotatedProperties
{
    private $reflection;

    public function __construct($objectOrClass)
    {
        $class = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        $this->reflection = new \ReflectionClass($class);

    }
}
