<?php

namespace Ephrin\DataObject\Tests\Stubs;

use Ephrin\DataObject\DocBlockProperties;

/**
 * @property string $stringProperty
 * @property integer $integerProperty
 * @property array $arrayProperty
 * @property bool $boolProperty
 * @property object $objectProperty
 * @property null $nullProperty
 */
class TypesStub
{
    use DocBlockProperties;
}
