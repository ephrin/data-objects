<?php

namespace Ephrin\Immutable;

trait DocProperties
{
    /** @var DocStructure */
    private $structure;

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getStructure()->readValue($name);
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
            $this->structure = DocStructureFactory::create($this);
        }

        return $this->structure;
    }

    /**
     * @internal array $data
     * @internal mixed $constructorArgs...
     * @return static
     */
    public static function fromArray()
    {
        $args = func_get_args();
        $data = array_shift($args);
        $instance = (new \ReflectionClass(static::class))->newInstanceArgs($args);
        $instance->structure = DocStructureFactory::create($instance, $data);
        return $instance;
    }
}
