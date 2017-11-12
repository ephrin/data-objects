<?php

namespace Ephrin\Immutable;

interface MetaReader
{
    /**
     * @param object $instance
     * @param array $defaults
     * @return Property[]
     */
    public function readMeta($instance, array $defaults = []);
}
