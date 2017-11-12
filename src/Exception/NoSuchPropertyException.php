<?php

namespace Ephrin\DataObject\Exception;

use Throwable;

class NoSuchPropertyException extends StructureException
{
    public function __construct($property, $class, Throwable $previous = null)
    {
        $message = sprintf('No such property `%s` in `%s`', $property, $class);
        parent::__construct($message, 0, $previous);
    }
}
