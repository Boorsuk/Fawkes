<?php

declare(strict_types = 1);

namespace Fawkes\Network;

use Fawkes\Enums\HttpMethod;

class Request
{
    private HttpMethod $method;

    private string $uri;

    private array $queryParams = [];

    private function __construct(HttpMethod $method, string $uri){
        $this->method = $method;
        
        $uriArr = explode('?', $uri);
        $this->uri = $uriArr[0];
        
        $queryParams = $uriArr[1] ?? null; 
        if($queryParams){
            parse_str($queryParams, $this->queryParams);
        }
    }

    public function getMethod() : HttpMethod{
        return $this->method;
    }

    public function getUri() : string{
        return $this->uri;
    }

    public function params() : array {
        return $this->queryParams;
    }

    public static function capture() {
        $uri    = $_SERVER['REQUEST_URI'];
        $method = HttpMethod::tryFrom(strtoupper($_SERVER['REQUEST_METHOD']));

        return new static($method, $uri);
    }
}