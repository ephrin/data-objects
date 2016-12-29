<?php

namespace Ephrin\Immutable;

class DocProperty
{
    /** @var string */
    public $type;

    /** @var boolean */
    public $writable;

    /** @var callable */
    public $valueGate;

    /**
     * @param string $type
     * @param boolean $writable
     * @param callable $valueGate
     */
    public function __construct($type, $writable, callable $valueGate = null)
    {
        $this->type = $type;
        $this->writable = $writable;
        $this->valueGate = $valueGate ?: [self::class, 'proxy'];
    }

    public static function proxy($value)
    {
        return $value;
    }
}
