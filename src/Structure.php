<?php

namespace Ephrin\Immutable;

use Ephrin\Immutable\Exception\NoSuchPropertyException;

class Structure
{
    /** @var Property[] */
    private $properties;

    /** @var string */
    private $class;

    /**
     * @param string $class FQCN
     * @param Property[] $properties
     * @param array $defaults
     */
    public function __construct($class, array $properties, array $defaults = [])
    {
        $this->init($class, $properties, $defaults);
    }

    /**
     * @param string $class
     * @param array $properties
     * @param array $defaults
     */
    private function init($class, array $properties, array $defaults)
    {
        $this->class = $class;
        foreach ($properties as $property) {
            /** @var Property $property */
            $meta = $property->getMeta();
            if (array_key_exists($meta->name, $defaults)) {
                $defaultValue = $defaults[$meta->name];

                if (is_array($defaultValue) && (class_exists($class) && method_exists($class, 'fromArray'))) {
                    $defaultValue = $class::fromArray($defaultValue);
                }

                $property->set($defaultValue);
            }
            $this->properties[$meta->name] = $property;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param string $name
     * @return Property|mixed
     * @throws \Ephrin\Immutable\Exception\NoSuchPropertyException
     */
    public function getProperty($name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }
        throw new NoSuchPropertyException($name, $this->class);
    }

    public function toArray()
    {
        $array = [];
        foreach ($this->properties as $name => $property) {
            if ($property->readable()) {
                $array[$name] = $property->get();
            }
        }

        return $array;
    }
}
