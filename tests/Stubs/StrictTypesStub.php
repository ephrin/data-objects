<?php

namespace Ephrin\DataObject\Tests\Stubs;

use Ephrin\DataObject\DocBlockProperties;
use Ephrin\DataObject\Type\Strict;

/**
 * @property string $stringProperty
 * @property integer $integerProperty
 * @property array $arrayProperty
 * @property bool $boolProperty
 * @property object $objectProperty
 * @property \stdClass $objectTypedProperty
 */
class StrictTypesStub implements Strict
{
    use DocBlockProperties;
}
