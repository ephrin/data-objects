<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\DocProperties;

/**
 * @property-read integer $integerHere
 * @property string  $stringSimple
 * @property-write callable $callbackWritable
 */
class ImmutableSimple
{
    use DocProperties;
    use Strict;


}
