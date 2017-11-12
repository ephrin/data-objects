<?php

namespace Ephrin\DataObject;

interface MetaReader
{
    /**
     * @param object $instance
     * @param array $defaults
     * @return PropertyMeta[]
     */
    public function readMeta($instance, array $defaults = []);
}
