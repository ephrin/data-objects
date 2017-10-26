<?php

namespace Ephrin\Immutable;

trait DocProperties
{
    /** @var DocStructure */
    private $structure;

    private $defaults = [];

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
        $instance->structure = DocStructureFactory::create($instance, $data);

        return $instance;
    }

    public function __construct(array $defaults = [])
    {
        $this->defaults = $defaults;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getStructure()->readValue($name);
    }

    protected function getValue($propertyName)
    {
        return $this->getStructure()->values[$propertyName];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->getStructure()->writeValue($name, $value);
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
     * @return DocStructure
     */
    protected function getStructure()
    {
        if (null === $this->structure) {
            $this->structure = DocStructureFactory::create($this, $this->getDefaults());
        }

        return $this->structure;
    }

    public function __debugInfo()
    {
        return $this->getStructure()->toArray();
    }
}
