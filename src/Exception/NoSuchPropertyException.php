<?php

namespace Ephrin\Immutable\Exception;

use Throwable;

class NoSuchPropertyException extends StructureException
{
    public function __construct($property, Throwable $previous = null)
    {
        $message = sprintf('No such property `%s`', $property);
        parent::__construct($message, 0, $previous);
    }
}
