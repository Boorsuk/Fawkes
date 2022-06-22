<?php

declare(strict_types = 1);

namespace Fawkes\Attributes;

use Fawkes\Enums\HttpMethod;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Route
{
    protected HttpMethod $method;
    protected string $uri;

    public function __construct(HttpMethod $method, string $uri)
    {
        $this->method = $method;
        $this->uri = $uri;
    }

    public function method() : HttpMethod
    {
        return $this->method;
    }

    public function uri() : string
    {
        return $this->uri;
    }
}