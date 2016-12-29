<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\StaticFactoryTrait;

class ParentStubClass
{
    use StaticFactoryTrait;

    /**
     * @var \stdClass
     */
    private $object;

    public function __construct(\stdClass $object)
    {
        $this->object = $object;
    }
}
