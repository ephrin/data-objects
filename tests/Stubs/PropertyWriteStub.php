<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\DocProperties;

/**
 * @property-write string $writeOnlyProperty
 */
class PropertyWriteStub
{
    use DocProperties;

    public function getWriteOnlyProperty()
    {
        return $this->getValue('writeOnlyProperty');
    }
}
