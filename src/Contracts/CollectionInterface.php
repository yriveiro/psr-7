<?php
namespace yriveiro\Psr7\Contracts;

interface CollectionInterface extends
    \Countable,
    \ArrayAccess,
    \IteratorAggregate
{
    public function get($name);
    public function has($name);
    public function remove($name);
    public function set($name, $value);
}
