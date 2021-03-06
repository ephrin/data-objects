<?php

namespace Ephrin\DataObject;

use Ephrin\DataObject\PropertyDriver\DocumentedPropertyExtractor;

trait DocBlockProperties
{
    /** @var Structure */
    private $structure;

    private $_defaults = [];

    /**
     * @internal array $data
     * @internal mixed $constructorArgs...
     * @return static
     */
    public static function fromArray()
    {
        static $reader;

        if (!$reader) {
            $reader = new DocumentedPropertyExtractor();
        }

        $args = func_get_args();
        $data = array_shift($args);
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = (new \ReflectionClass(static::class))->newInstanceArgs($args);

        $instance->structure = StructureFactory::create($reader, $instance, $data);

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
     * @throws \Ephrin\DataObject\Exception\NoSuchPropertyException
     * @throws \Ephrin\DataObject\Exception\PropertyAccessException
     */
    public function __get($name)
    {
        return $this->getStructure()->getProperty($name)->tryRead();
    }
    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param $propertyName
     * @return mixed
     */
    protected function getValue($propertyName)
    {
        return $this->getStructure()->getProperty($propertyName)->get();
    }

    /**
     * @param $propertyName
     * @param $value
     * @throws Exception\NoSuchPropertyException
     */
    protected function setValue($propertyName, $value)
    {
        $this->getStructure()->getProperty($propertyName)->set($value);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws Exception\NoSuchPropertyException
     * @throws Exception\PropertyAccessException
     */
    public function __set($name, $value)
    {
        $this->getStructure()->getProperty($name)->tryWrite($value);
    }

    /**
     * @param string $name
     * @return bool
     * @throws Exception\NoSuchPropertyException
     */
    public function __isset($name)
    {
        $structure = $this->getStructure();

        if ($structure->hasProperty($name)) {
            return false === $this->getStructure()->getProperty($name)->equals(null);
        }

        return false;
    }

    /**
     * @param string $name
     * @throws Exception\NoSuchPropertyException
     * @throws Exception\PropertyAccessException
     */
    public function __unset($name)
    {
        $structure = $this->getStructure();
        if ($structure->hasProperty($name)) {
            $structure->getProperty($name)->tryUnset();
        }
    }

    /**
     * @return Structure
     * @throws \InvalidArgumentException
     */
    protected function getStructure()
    {
        if (null === $this->structure) {
            $this->structure = StructureFactory::create(new DocumentedPropertyExtractor(), $this, $this->getDefaults());
        }

        return $this->structure;
    }

    public function __debugInfo()
    {
        return $this->getStructure()->toArray();
    }
}
