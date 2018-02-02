<?php

declare(strict_types=1);

namespace MailExport;

class Map implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * @var array
     */
    private $map = [];

    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    public function keys()
    {
        return array_keys($this->map);
    }

    public function values()
    {
        return array_values($this->map);
    }

    public function toArray()
    {
        return $this->map;
    }

    public function clear()
    {
        $this->map = [];
    }

    public function offsetExists($offset)
    {
        return isset($this->map[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->map[$offset]) ? $this->map[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->map[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->map[$offset]);
    }

    public function count()
    {
        return count($this->map);
    }

    public function current()
    {
        return current($this->map);
    }

    public function key()
    {
        return key($this->map);
    }

    public function next()
    {
        next($this->map);
    }

    public function rewind()
    {
        reset($this->map);
    }

    public function valid()
    {
        return current($this->map) !== false;
    }
}
