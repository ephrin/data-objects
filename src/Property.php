<?php

namespace Ephrin\Immutable;

class Property
{
    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var boolean */
    public $writable;

    /** @var boolean */
    public $readable;

    /** @var callable */
    public $valueGate;

    /** @var mixed */
    public $defaultValue;

    /** @var mixed */
    public $value;
}
