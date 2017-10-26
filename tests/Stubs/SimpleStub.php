<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\DocProperties;

/**
 * @property string $stringProperty
 * @property-read integer $integerProperty
 */
class SimpleStub
{
    use DocProperties;
}
