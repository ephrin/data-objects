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
        return $this->getStructure()->readProperty($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->getStructure()->writeProperty($name, $value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->getStructure()->hasProperty($name);
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
    private function getStructure()
    {
        if (null === $this->structure) {
            $this->structure = StructureFactory::create($this);
        }

        return $this->structure;
    }
}
