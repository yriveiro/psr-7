<?php
namespace yriveiro\Psr7;

function normalize($key)
{
    return strtolower(str_replace("_", "-", trim($key)));
}

function is_array_strings($values)
{
    return array_reduce($values, function ($carry, $item) {
        return (!is_string($item)) ? false : $carry;
    }, true);
}
