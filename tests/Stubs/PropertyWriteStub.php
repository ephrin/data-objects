<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\DocBlockProperties;

/**
 * @property-write string $writeOnlyProperty
 */
class PropertyWriteStub
{
    use DocBlockProperties;

    public function getWriteOnlyProperty()
    {
        return $this->getValue('writeOnlyProperty');
    }
}
