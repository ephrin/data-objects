<?php

namespace Ephrin\Immutable;

class PropertyMeta
{
    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var string */
    public $owner;

    /** @var boolean */
    public $readable;

    /** @var boolean */
    public $writable;

    /** @var string */
    public $valueGateFactoryClass = DefaultGateFactory::class;

    protected $valueGate;

    public function pass($value)
    {
        if (!$this->valueGate) {
            $this->valueGate = call_user_func(
                $this->valueGateFactoryClass,
                $this->type, $this->owner, $this->name
            );
        }

        return call_user_func($this->valueGate, $value);
    }
}
