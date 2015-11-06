<?php
/**
 * Date: 15/10/13
 * Time: 20:05
 */

namespace Hoimi;


class ArrayContainer implements Gettable, \ArrayAccess, \Countable, \Serializable
{
    protected $array = null;
    public function __construct (array $array)
    {
        $this->array = $array;
    }

    public function get($key, $default = null)
    {
        if (!isset($key)) {
            throw new \InvalidArgumentException('$key is undefined');
        }
        if (isset($this->array[$key])) return $this->array[$key];
        $tmp = $this->array;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($tmp) || !isset($tmp[$segment])){
                $tmp = null;
                break;
            } elseif (isset($tmp[$segment])) {
                $tmp = $tmp[$segment];
            } else {
                $tmp = null;
                break;
            }
        }
        return isset($tmp) ? $tmp : $default;
    }

    /**
     * @return array|null
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->array);
    }

    /**
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->array);
    }

    /**
     * @param string $serialized <p>The string representation of the object.</p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->array = unserialize($serialized);
    }
}