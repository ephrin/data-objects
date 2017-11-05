<?php

namespace Ephrin\Immutable\Tests\Stubs;

use Ephrin\Immutable\DocBlockProperties;

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
