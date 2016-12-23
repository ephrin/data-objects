<?php

namespace Ephrin\Immutable;

class PropertyType
{
    public function __construct($type, $validation, $writable)
    {
        $this->type = $type;
        $this->validation = $validation;
        $this->writable = $writable;
    }

    /** @var string */
    public $type;

    /** @var boolean */
    public $writable;

    /** @var callable */
    public $validation;
}
