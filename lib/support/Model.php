<?php

namespace MocOrm\Support;

/**
 * Model
 */
abstract class Model extends \MocOrm\Model\Model implements \ArrayAccess
{
    /**
     * Gets model data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getData();
    }

    /**
     * Gets model data through static method.
     *
     * @param  mixed $data
     * @return array
     */
    public static function toList($data)
    {
        if (!($data instanceof self)) throw new \Exception(" It's not a model.");

        $data = array_map(function ($object) { return $object->toArray(); }, $data);

        return $data;
    }

    /**
     * Get first model from query result.
     *
     * @return mixed
     */
    public function first()
    {
        return current($this->getData());
    }

    /**
     * ArrayAccess Interface.
     */
    public function offsetExists($offset)
    {
        $data = $this->getData();

        return isset($data[$offset]);
    }

    public function offsetGet($offset)
    {
        $data = $this->getData();

        return isset($data[$offset]) ? $data[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $data = $this->getData();

        if (is_null($offset)) {
            $data[] = $value;
        } else {
            $data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        $data = $this->getData();

        unset($this->data[$offset]);
    }
}
