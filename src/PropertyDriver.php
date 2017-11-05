<?php

namespace Ephrin\Immutable;

interface PropertyDriver
{
    /**
     * @param object $instance
     * @param array $defaults
     * @return Property[]|\Generator
     */
    public function properties($instance, array $defaults = []);
}
