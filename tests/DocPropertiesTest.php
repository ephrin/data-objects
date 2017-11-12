<?php

namespace Ephrin\DataObject\Tests;

use Ephrin\DataObject\Tests\Stubs\DataWithDefaultsInMethod;
use Ephrin\DataObject\Tests\Stubs\PropertyWriteStub;
use Ephrin\DataObject\Tests\Stubs\SimpleStub;

class DocPropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testReadConstructDefaults()
    {
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
        $pws = new PropertyWriteStub(['writeOnlyProperty' => 'initial value']);

        self::assertSame('initial value', $pws->getWriteOnlyProperty());

        $pws->writeOnlyProperty = 'changed value';

        self::assertSame('changed value', $pws->getWriteOnlyProperty());
    }

    public function testDefaultsInMethod()
    {
        $obj = new DataWithDefaultsInMethod();
        self::assertSame('simple', $obj->type);
    }
}
