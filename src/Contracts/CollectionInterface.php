<?php
namespace yriveiro\Psr7\Contracts;

interface CollectionInterface extends
    \Countable,
    \ArrayAccess,
    \IteratorAggregate
{
    public function clear();
    public function get($name);
    public function has($name);
    public function isEmpty();
    public function prepend($key, $value);
    public function remove($name);
    public function set($name, $value);
}
