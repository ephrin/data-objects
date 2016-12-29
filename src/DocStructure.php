<?php

namespace Ephrin\Immutable;

class DocStructure
{
    /** @var DocProperty[] */
    private $properties;

    /** @var array */
    private $values;

    /** @var string */
    private $class;

    /**
     * @param string $class FQCN
     * @param DocProperty[] $properties
     * @param array $defaults
     */
    public function __construct($class, array $properties, array $defaults = [])
    {
        $this->class = $class;
        $this->properties = $properties;
        $this->values = array_intersect_key($defaults, $properties);
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
     * @param mixed $propertyName
     * @return mixed|null
     * @throws \RuntimeException
     */
    public function readValue($propertyName)
    {
        if (isset($this->properties[$propertyName])) {
            return isset($this->values[$propertyName]) ? $this->values[$propertyName] : null;
        }

        throw new \RuntimeException(
            sprintf('DocProperty `%s` of `%s` does not exists', $propertyName, $this->class)
        );
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @throws \RuntimeException
     */
    public function writeValue($propertyName, $value)
    {
        if (!isset($this->properties[$propertyName])) {
            throw new \RuntimeException(
                sprintf('Can not access. No such property `%s` of `%s`.', $propertyName, $this->class)
            );
        }
        if (!$this->properties[$propertyName]->writable) {
            throw new \RuntimeException(
                sprintf('Can not store value. DocProperty `%s` of `%s` is not writable.', $propertyName, $this->class)
            );
        }

        $this->values[$propertyName] = call_user_func($this->properties[$propertyName]->valueGate, $value);
    }
}
