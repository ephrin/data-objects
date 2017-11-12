<?php

namespace Ephrin\DataObject\Tests\Stubs;

use Ephrin\DataObject\DocBlockProperties;

/**
 * @property integer $id
 * @property string $type
 */
class DataWithDefaultsInMethod {
    use DocBlockProperties;

    public function getDefaults()
    {
        return ['type' => 'simple'];
    }
}
