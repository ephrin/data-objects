<?php

namespace Ephrin\Immutable\Tests\Stubs;

class ChildStubClass extends ParentStubClass
{
    public function __construct(\stdClass $class, ParentStubClass $parentStubClass)
    {
        parent::__construct($class);
    }
}
