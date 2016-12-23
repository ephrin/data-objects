<?php

namespace Ephrin\Immutable\Tests;

use Ephrin\Immutable\Tests\Stubs\ImmutableSimple;

class ImmutableTestCase extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $simple = new ImmutableSimple();
        $r = $simple->integerHere * 5;
        $this->assertSame($simple, $simple);
    }
}
