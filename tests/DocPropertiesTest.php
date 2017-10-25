<?php

namespace Ephrin\Immutable\Tests;

use Ephrin\Immutable\Tests\Stubs\SimpleStub;

class DocPropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testReadConstructDefaults()
    {
        $simple = SimpleStub::fromArray(
            [
                'stringProperty' => 'string', //usual
                'integerProperty' => 42, //read only
                'booleanProperty' => true //write annotated
            ]
        );

        self::assertSame('string', $simple->stringProperty);
        self::assertSame(42, $simple->integerProperty);
        self::assertSame(true, $simple->booleanProperty);
    }
}
