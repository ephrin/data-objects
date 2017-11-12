<?php

namespace Ephrin\DataObject\Tests\Stubs;

use Ephrin\DataObject\DocBlockProperties;

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
