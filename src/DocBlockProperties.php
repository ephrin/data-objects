<?php

namespace Ephrin\Immutable;

use Ephrin\Immutable\PropertyDriver\DocPropertyMetaReader;

trait DocBlockProperties
{
    /** @var Structure */
    private $structure;

    private $_defaults = [];

    /**
     * @internal array $data
     * @internal mixed $constructorArgs...
     * @return static
     * @throws \ReflectionException
     */
    public static function fromArray()
    {
        $args = func_get_args();
        $data = array_shift($args);
        $instance = (new \ReflectionClass(static::class))->newInstanceArgs($args);

        $driver = new DocPropertyMetaReader();
        $instance->structure = StructureFactory::create($driver, $instance, $data);

        return $instance;
    }

    public function __construct(array $defaults = [])
    {
        $this->_defaults = $defaults;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \Ephrin\Immutable\Exception\NoSuchPropertyException
     * @throws \Ephrin\Immutable\Exception\PropertyAccessException
     */
    public function __get($name)
    {
        return $this->getStructure()->getProperty($name)->tryRead();
    }

    protected function getValue($propertyName)
    {
        return $this->getStructure()->getProperty($propertyName)->value;
    }

    protected function setValue($propertyName, $value)
    {
        $property = $this->getStructure()->getProperty($propertyName);

        $property->value = call_user_func($property->valueGate, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->getStructure()->trySet($name, $value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->getStructure()->issetValue($name);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        $this->getStructure()->unsetValue($name);
    }

    /**
     * @return Structure
     * @throws \InvalidArgumentException
     */
    protected function getStructure()
    {
        if (null === $this->structure) {
            $this->structure = StructureFactory::create(new DocPropertyMetaReader(), $this, $this->getDefaults());
        }

        return $this->structure;
    }

    public function __debugInfo()
    {
        return $this->getStructure()->toArray();
    }
}
