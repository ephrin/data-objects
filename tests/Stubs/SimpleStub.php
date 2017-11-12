<?php

namespace Ephrin\DataObject\Tests\Stubs;

use Ephrin\DataObject\DocBlockProperties;

/**
 * @property string $stringProperty
 * @property-read integer $integerProperty
 */
class SimpleStub
{
    use DocBlockProperties;
}
