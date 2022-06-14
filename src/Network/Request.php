<?php

declare(strict_types = 1);

namespace Fawkes\Network;

class Request
{
    private string $method;

    private string $uri;

    private array $queryParams = [];

    public function __construct(string $method, string $uri){
        $this->method = mb_strtoupper($method);
        
        $uriArr = explode('?', $uri);
        $this->uri = $uriArr[0];
        
        $queryParams = $uriArr[1] ?? null; 
        if($queryParams){
            parse_str($queryParams, $this->queryParams);
        }
    }

    public function getMethod() : string{
        return $this->method;
    }

    public function getUri() : string{
        return $this->uri;
    }
}