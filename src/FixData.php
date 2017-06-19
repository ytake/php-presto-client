<?php
declare(strict_types=1);

namespace Ytake\PrestoClient;

/**
 * Class FixData
 */
final class FixData implements \ArrayAccess
{
    /**
     * @param string $column
     * @param        $value
     */
    public function add(string $column, $value)
    {
        $this->$column = $value;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * @param string $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->$offset ?? null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
}
