<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\DocBlockProperties;

/**
 * @property string $stringProperty
 * @property-read integer $integerProperty
 */
class SimpleStub
{
    use DocBlockProperties;
}
