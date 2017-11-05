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
     * @throws \InvalidArgumentException
     */
    public function __construct($class, array $properties)
    {
        $this->class = $class;
        $this->init($properties);
    }

    /**
     * @param array|Property[] $properties
     */
    private function init(array $properties)
    {
        foreach ($properties as $property) {
            if (null !== $property->defaultValue) {
                $type = $property->type;
                if (is_array($property->defaultValue) && (class_exists($type) && method_exists($type, 'fromArray'))) {
                    $defaultValue = $type::fromArray($property->defaultValue);
                } else {
                    $defaultValue = $property->defaultValue;
                }
                $property->value = call_user_func(
                    $property->valueGate,
                    $defaultValue
                );
            }
            $this->properties[$property->name] = $property;
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
        throw new NoSuchPropertyException($name);
    }

    public function issetValue($propertyName)
    {
        if (isset($this->properties[$propertyName])) {
            if (false === $this->properties[$propertyName]->readable) {
                return false;
            }

            return null !== $this->properties[$propertyName]->value;
        }

        return false;
    }

    public function unsetValue($propertyName)
    {
        if (isset($this->properties[$propertyName])) {
            unset($this->properties[$propertyName]->value);
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
                        $this->class,
                        $propertyName
                    )
                );
            }

            return $this->properties[$propertyName]->value;
        }

        throw new \RuntimeException(
            sprintf(
                'Property `%s` of `%s` does not exists or not declared.',
                $propertyName,
                $this->class
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
                sprintf('No such property `%s` of `%s`.', $propertyName, $this->class)
            );
        }
        $property = $this->properties[$propertyName];
        if (false === $property->writable) {
            throw new \RuntimeException(
                sprintf(
                    'Can not store value into property `%s` of `%s`. It is not writable.',
                    $propertyName,
                    $this->class
                )
            );
        }

        $property->value = call_user_func($property->valueGate, $value);
    }

    public function toArray()
    {
        $array = [];
        foreach ($this->properties as $property) {
            if ($property->readable) {
                $array[$property->name] = $property->value;
            }
        }

        return $array;
    }
}
