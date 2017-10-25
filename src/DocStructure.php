<?php

namespace Ephrin\Immutable;

class DocStructure
{
    /** @var DocProperty[] */
    private $properties;

    /** @var array */
    private $values = [];

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

        if (!method_exists($this->instance, 'defaults')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Inproper instance retrieved. Please make sure %s trait is implemented in %s',
                    DocProperties::class,
                    get_class($this->instance)
                )
            );
        }

        $this->properties = $properties;
        $this->init(array_merge_recursive(call_user_func([$this->instance, 'defaults']), $defaults));
    }

    private function init(array $defaults = [])
    {
        foreach ($defaults as $propertyName => $value) {
            if (isset($this->properties[$propertyName])) {
                $type = $this->properties[$propertyName];
                if (class_exists($type) && method_exists($type, 'fromArray')) {
                    if(is_array($value)){
                        $value = call_user_func([$type, 'fromArray'], $value);
                    }
                }
                $this->values[$propertyName] = $this->properties[$propertyName]->valueGate($value);
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

        if(false === $this->properties[$propertyName]->readable){
            throw new \RuntimeException(
                sprintf(
                    'Can not access value of property `%s` in `%s` as it is not readable.',
                    $propertyName,
                    get_class($this->instance)
                )
            );
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

        $this->values[$propertyName] = $this->properties[$propertyName]->valueGate($value);
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
