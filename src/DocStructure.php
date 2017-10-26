<?php

namespace Ephrin\Immutable;

class DocStructure
{
    /** @var DocProperty[] */
    private $properties;

    /** @var array */
    public $values = [];

    /** @var string */
    private $instance;

    /**
     * @param string $instance FQCN
     * @param DocProperty[] $properties
     * @param array $defaults
     * @throws \InvalidArgumentException
     */
    public function __construct($instance, array $properties, array $defaults = [])
    {
        $this->instance = $instance;
        $this->properties = $properties;
        $this->init($defaults);
    }

    private function init(array $defaults = [])
    {
        foreach ($defaults as $propertyName => $defaultValue) {
            if (isset($this->properties[$propertyName])) {
                $type = $this->properties[$propertyName]->type;

                if (class_exists($type) && method_exists($type, 'fromArray')) {
                    if (is_array($defaultValue)) {
                        $defaultValue = call_user_func([$type, 'fromArray'], $defaultValue);
                    }
                }
                $this->values[$propertyName] = call_user_func($this->properties[$propertyName]->valueGate,
                    $defaultValue);
            }
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

    public function issetValue($propertyName)
    {
        if (false === $this->properties[$propertyName]->readable) {
            return false;
        }

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
            if (false === $this->properties[$propertyName]->readable) {
                throw new \RuntimeException(
                    sprintf(
                        'Can not read property `%s->$%s`. Write only access.',
                        get_class($this->instance),
                        $propertyName
                    )
                );
            }

            return isset($this->values[$propertyName]) ? $this->values[$propertyName] : null;
        }

        throw new \RuntimeException(
            sprintf(
                'Property `%s` of `%s` does not exists or not declared.',
                $propertyName,
                get_class($this->instance)
            )
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
                sprintf('No such property `%s` of `%s`.', $propertyName, get_class($this->instance))
            );
        }
        if (false === $this->properties[$propertyName]->writable) {
            throw new \RuntimeException(
                sprintf(
                    'Can not store value into property `%s` of `%s`. It is not writable.',
                    $propertyName,
                    get_class($this->instance)
                )
            );
        }

        $this->values[$propertyName] = call_user_func($this->properties[$propertyName]->valueGate, $value);
    }

    public function toArray()
    {
        $array = [];
        foreach (array_keys($this->properties) as $propertyName) {
            $array[$propertyName] = $this->readValue($propertyName);
        }

        return $array;
    }
}
