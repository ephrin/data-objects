<?php

namespace Ephrin\DataObject\Tests;

use Ephrin\DataObject\Exception\PropertyAccessException;
use Ephrin\DataObject\Tests\Stubs\DataWithDefaultsInMethod;
use Ephrin\DataObject\Tests\Stubs\PropertyWriteStub;
use Ephrin\DataObject\Tests\Stubs\SimpleStub;
use Ephrin\DataObject\Tests\Stubs\StrictTypesStub;
use Ephrin\DataObject\Tests\Stubs\TypesStub;

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

    /** @dataProvider casts
     * @param string $property
     * @param mixed $initialVal
     * @param mixed $expected
     * @param bool $same
     */
    public function testCast(string $property, $initialVal, $expected, $same = true)
    {
        $obj = new TypesStub();

        $obj->{$property} = $initialVal;

        $val = $obj->{$property};

        if ($same) {
            self::assertSame($expected, $val);
        } else {
            self::assertEquals($expected, $val);
        }
    }

    public function casts()
    {
        yield 'str to int' => [
            'integerProperty',
            '42',
            42
        ];

        yield 'str to int' => [
            'integerProperty',
            '42a',
            42
        ];

        yield 'int to str' => [
            'stringProperty',
            0,
            '0'
        ];

        yield 'int to str' => [
            'stringProperty',
            42,
            '42'
        ];

        yield 'str to bool: false' => [
            'boolProperty',
            '',
            false
        ];

        yield 'str to bool: true' => [
            'boolProperty',
            'a',
            true
        ];

        yield 'int to bool: false' => [
            'boolProperty',
            0,
            false
        ];

        yield 'int to bool: true' => [
            'boolProperty',
            1,
            true
        ];

        yield 'arr to bool: false' => [
            'boolProperty',
            [],
            false
        ];

        yield 'arr to bool: true' => [
            'boolProperty',
            [1],
            true
        ];

        yield 'null to bool: false' => [
            'boolProperty',
            null,
            false
        ];

        yield 'null to arr' => [
            'arrayProperty',
            null,
            []
        ];

        yield 'arr to arr' => [
            'arrayProperty',
            [1, 2, 3],
            [1, 2, 3]
        ];

        yield 'str to arr' => [
            'arrayProperty',
            'a',
            ['a']
        ];

        $obj = (object)['a' => 1];
        yield 'obj to arr' => [
            'arrayProperty',
            $obj,
            ['a' => 1]
        ];

        yield 'arr to obj' => [
            'objectProperty',
            ['a' => 1],
            (object) ['a' => 1],
            false //strict equality is not possible
        ];

        yield 'int to obj' => [
            'objectProperty',
            1,
            (object) ['scalar' => 1],
            false //strict equality is not possible
        ];

        $obj = (object)['a' => 42];
        yield 'int to obj' => [
            'objectProperty',
            $obj,
            $obj
        ];
    }

    /** @dataProvider strictDataFailures
     * @param string $property
     * @param mixed $initialVal
     * @param string $expectedType
     * @param string $gotType
     */
    public function testStrictFailures(string $property, $initialVal, $expectedType, $gotType)
    {
        $obj = new StrictTypesStub();


        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage(
            sprintf('Can not assign value with type `%s` to property %s->%s. Type of `%s` is expected.',
                $gotType,
                StrictTypesStub::class,
                $property,
                $expectedType)
        );

        $obj->{$property} = $initialVal;


    }

    public function strictDataFailures()
    {
        yield 'str to int' => [
            'integerProperty',
            '42',
            'int',
            'string',
        ];

        yield 'int to str' => [
            'stringProperty',
            42,
            'string',
            'integer',
        ];

        yield 'int to str' => [
            'stringProperty',
            42,
            'string',
            'integer',
        ];

        yield 'int to arr' => [
            'arrayProperty',
            42,
            'array',
            'integer'
        ];

        yield 'obj to arr' => [
            'arrayProperty',
            (object)['a' => 1],
            'array',
            \stdClass::class
        ];

        yield 'arr to obj' => [
            'objectProperty',
            ['a' => 1],
            'object',
            'array'
        ];

        yield 'different instanceof' => [
            'objectTypedProperty',
            new \ArrayObject(),
            \stdClass::class,
            \ArrayObject::class
        ];
    }

    public function testErrorOnWriteImmutable()
    {
        $obj = new SimpleStub(['integerProperty' => 42]);

        self::assertEquals(42, $obj->integerProperty);

        self::expectException(PropertyAccessException::class);

        //to disable IDE warnings in source code (as @nointention disabling does not work but we need an IDE annotator)
        $setIntegerProperty = function ($object, $val) {
            $object->integerProperty = $val;
        };
        $setIntegerProperty($obj, 42);
    }
}
