<?php

namespace Ephrin\Immutable;

trait StaticFactoryTrait
{
    public static function create()
    {
        return (new \ReflectionClass(static::class))->newInstanceArgs(func_get_args());
    }
}
