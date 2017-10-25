<?php

namespace Ephrin\Immutable;

class DocProperty
{
    /** @var string */
    public $type;

    /** @var boolean */
    public $writable;

    /** @var boolean */
    public $readable;

    /** @var callable */
    public $valueGate;

    /**
     * @param string $type
     * @param boolean $writable
     * @param boolean $readable
     * @param callable $valueGate
     */
    public function __construct($type, $writable, $readable, callable $valueGate)
    {
        $this->type = $type;
        $this->writable = $writable;
        $this->readable = $readable;
        $this->valueGate = $valueGate;
    }
}
