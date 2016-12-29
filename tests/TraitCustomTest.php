<?php

namespace Ephrin\Immutable\Tests;

use Ephrin\Immutable\Tests\Stubs\ParentStubClass;
use Ephrin\Immutable\Tests\Stubs\ChildStubClass;

class TraitCustomTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructionInTrait()
    {
        $instance = ParentStubClass::create((object)[]);

        $this->assertInstanceOf(ParentStubClass::class, $instance);

        $instance2 = ChildStubClass::create((object)[], new ParentStubClass((object)[]));

        $this->assertInstanceOf(ChildStubClass::class, $instance2);
    }
}
