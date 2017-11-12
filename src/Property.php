<?php

namespace Ephrin\Immutable;

use Ephrin\Immutable\Exception\PropertyAccessException;

class Property
{
    /** @var Property */
    protected $meta;

    /** @var mixed */
    protected $value;

    public function __construct(PropertyMeta $meta, $value)
    {
        $this->meta = $meta;
        $this->value = $value;
    }

    /**
     * @return Property
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function set($value)
    {
        $this->value = $this->meta->pass($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function equals($value)
    {
        return $this->value === $value;
    }

    /**
     * @param mixed $value
     * @throws PropertyAccessException
     */
    public function tryWrite($value)
    {
        if (false === $this->meta->writable) {
            throw new PropertyAccessException(
                sprintf('Property `%s->$%s` is not writable.', $this->meta->owner, $this->meta->name)
            );
        }

        $this->set($value);
    }

    /**
     * @return mixed
     * @throws PropertyAccessException
     */
    public function tryRead()
    {
        if (false === $this->meta->readable) {
            throw new PropertyAccessException(
                sprintf('Property `%s->$%s` is not readable.', $this->meta->owner, $this->meta->name)
            );
        }

        return $this->value;
    }

    public function readable()
    {
        return $this->meta->readable === true;
    }
}
