<?php
namespace yriveiro\Psr7;

use InvalidArgumentException;

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

function is_valid_method($method)
{
    if (null === $method) {
        return;
    }

    if (!is_string($method)) {
        throw new InvalidArgumentException(sprintf(
            'Unsupported HTTP method; must be a string, received %s',
            (is_object($method) ? get_class($method) : gettype($method))
        ));
    }

    if (!preg_match('/^[!#$%&\'*+.^_`\|~0-9a-z-]+$/i', $method)) {
        throw new InvalidArgumentException(sprintf(
            'Unsupported HTTP method "%s" provided',
            $method
        ));
    }
}

function type($var)
{
    return (is_object($var) ? get_class($var) : gettype($var));
}
