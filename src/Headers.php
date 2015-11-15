<?php
namespace yriveiro\Psr7;

use ArrayIterator;
use yriveiro\Psr7;
use yriveiro\Psr7\Contracts\HeadersInterface;

class Headers implements HeadersInterface
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * @return void
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $name => $values) {
            $this->set($name, $values);
        }
    }

    // ArrayAccess interface

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists(Psr7\normalize($key), $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items[Psr7\normalize($key)]["values"];
    }

    /**
     * Set the item(s) at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $values
     * @return void
     */
    public function offsetSet($key, $values)
    {
        if (!is_array($values)) {
            $value = [$values];
        }

        $normalized = Psr7\normalize($key);

        if ($this->has($key)) {
            $diff = array_diff($values, $this->items[$normalized]["values"]);

            if (!empty($diff)) {
                foreach ($diff as $value) {
                    array_push($this->items[$normalized]["values"], $value);
                }
            }
        } else {
            $this->items[$normalized] = ["name" => $key, "values" => $values];
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->items[Psr7\normalize($key)]);
    }

    // Countable interface

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    // IteratorAggregate interface

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all()
    {
        return $this->getIterator();
    }

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return $default;
    }

    /**
     * Set an item in the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return $this
     */
    public function set($key, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Prepend an item at the beginning of collection.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return $this
     */
    public function prepend($key, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->items += [$key, $value];

        return $this;
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Remove an item or items from the collection by key.
     *
     * @param  string|array  $keys
     * @return $this
     */
    public function remove($keys)
    {
        foreach ((array) $keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Remove all items from the collection.
     *
     * @return $this
     */
    public function clear()
    {
        $this->items = [];

        return $this;
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        // TODO: implement this method.
    }
}
