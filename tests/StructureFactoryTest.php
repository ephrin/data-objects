<?php

namespace Ephrin\Immutable\Tests;

use Ephrin\Immutable\StructureFactory;
use Ephrin\Immutable\Tests\Stubs\ImmutableSimple;

class StructureFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testStructureFactory()
    {
        $docStructure = StructureFactory::create(ImmutableSimple::class);

        $this->assertFalse($docStructure->hasWritableProperty('integerHere'));
        $this->assertTrue($docStructure->hasProperty('integerHere'));

        $this->assertTrue($docStructure->hasWritableProperty('stringSimple'));
        $this->assertTrue($docStructure->hasWritableProperty('callbackWritable'));
    }
}
