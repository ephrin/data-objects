<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\DocProperties;

/**
 * @property string  $stringProperty
 * @property-read integer $integerProperty
 * @property-write boolean $booleanProperty
 */
class SimpleStub
{
    use DocProperties;
}
