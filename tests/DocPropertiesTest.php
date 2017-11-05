<?php

namespace Ephrin\Immutable\Tests;

use Ephrin\Immutable\Tests\Stubs\DataWithDefaultsInMethod;
use Ephrin\Immutable\Tests\Stubs\PropertyWriteStub;
use Ephrin\Immutable\Tests\Stubs\SimpleStub;

class DocPropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testReadConstructDefaults()
    {
        // Class docBlock annotations
        // @property string $stringProperty
        // @property-read integer $integerProperty
        $simple = SimpleStub::fromArray(
            [
                'stringProperty' => 'string', //usual
                'integerProperty' => 42, //read only
            ]
        );

        self::assertSame('string', $simple->stringProperty);
        self::assertSame(42, $simple->integerProperty);
    }

    public function testWriteOnlyPropertyWrites()
    {
        // Class annotations
        // @property-write string $writeOnlyProperty
        $pws = new PropertyWriteStub(['writeOnlyProperty' => 'initial value']);

        self::assertSame('initial value', $pws->getWriteOnlyProperty());

        $pws->writeOnlyProperty = 'changed value';

        self::assertSame('changed value', $pws->getWriteOnlyProperty());
    }

    public function testWriteCast()
    {
        
    }

    public function testWriteOnlyPropertyException()
    {
        // Class annotations
        // @property-write string $writeOnlyProperty
        $pws = new PropertyWriteStub(['writeOnlyProperty' => 'initial value']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Can not read property');

        $propertyName = 'writeOnlyProperty'; //to ignore IDE error we will use var prop access
        $var = $pws->{$propertyName};
    }

    public function testDefaultsInMethod()
    {
        $obj = new DataWithDefaultsInMethod();
        self::assertSame('simple', $obj->type);
    }
}
