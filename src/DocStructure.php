<?php

namespace Ephrin\Immutable;

class DocStructure
{
    /** @var PropertyType[] */
    private $properties;

    /** @var array */
    private $values;

    /** @var string */
    private $class;

    /**
     * @param string $class FQCN
     * @param array $properties
     * @param array $defaults
     */
    public function __construct($class, array $properties, array $defaults = [])
    {
        $this->class = $class;
        $this->properties = $properties;
        $this->values = $defaults;
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
     * @return bool
     */
    public function hasWritableProperty($name)
    {
        return isset($this->properties[$name]) && $this->properties[$name]->writable;
    }

    public function issetValue($propertyName)
    {
        return isset($this->values[$propertyName]);
    }

    public function unsetValue($propertyName)
    {
        if (isset($this->values[$propertyName])) {
            unset($this->values[$propertyName]);
        }
    }

    /**
     * @param mixed $name
     * @return mixed|null
     * @throws \RuntimeException
     */
    public function readProperty($name)
    {
        if (isset($this->properties[$name])) {
            return isset($this->values[$name]) ? $this->values[$name] : null;
        }

        throw new \RuntimeException(
            sprintf('Property `%s` of `%s` does not exists', $name, $this->class)
        );
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \RuntimeException
     */
    public function writeProperty($name, $value)
    {
        if (!isset($this->properties[$name])) {
            throw new \RuntimeException(
                sprintf('Can not access. No such property %s of %s.', $name, $this->class)
            );
        }
        if (!$this->properties[$name]->writable) {
            throw new \RuntimeException(
                sprintf('Can not store value. Property %s of %s is not writable.', $name, $this->class)
            );
        }

        $this->values[$name] = $value;
    }
}
