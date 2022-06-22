<?php

declare(strict_types = 1);

namespace Fawkes\Enums;

enum HttpMethod: string
{
    case POST   = 'POST';
    case GET    = 'GET';
    case DELETE = 'DELETE';
    case PUT    = 'PUT';

    public function toString() : string
    {
        return $this->value;
    }
}