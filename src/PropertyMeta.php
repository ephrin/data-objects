<?php

namespace Ephrin\DataObject;

class PropertyMeta
{
    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var string */
    public $owningClass;

    /** @var boolean */
    public $readable;

    /** @var boolean */
    public $writable;

    /** @var string */
    public $valueGateFactoryClass = DefaultGateFactory::class;

    protected $valueGate = [];

    public function __construct(string $name, string $owningClass, string $type = null)
    {
        $this->name = $name;
        $this->owningClass = $owningClass;
        $this->type = $type;
    }

    public function pass($value)
    {
        if (!isset($this->valueGate[$this->valueGateFactoryClass])) {
            $this->valueGate[$this->valueGateFactoryClass] = call_user_func(
                [$this->valueGateFactoryClass, 'create'],
                $this->type, $this->owningClass, $this->name
            );
        }

        return call_user_func($this->valueGate[$this->valueGateFactoryClass], $value);
    }
}
