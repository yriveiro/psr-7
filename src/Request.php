<?php
namespace yriveiro\Psr7;

use yriveiro\Psr7\Traits\MessageTrait;
use yriveiro\Psr7\Headers;

class Request
{
    use MessageTrait;

    public function __construct(Headers $headers)
    {
        $this->headers = $headers;
    }
}
