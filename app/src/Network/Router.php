<?php

declare(strict_types = 1);

namespace Fawkes\Network;

use Fawkes\Exceptions\RouteNotFoundException;
use Fawkes\Network\Request;

class Router
{
    protected $routes = [];

    public function register(string $method, string $uri, array|callable $callback) : self{
        $method = mb_strtoupper($method);

        $this->routes[$method][$uri] = $callback;
        return $this;
    }

    public function resolve(Request $request) : mixed{
        $method = $request->getMethod();
        $uri    = $request->getUri();

        $callback = $this->routes[$method][$uri] ?? null;
        
        if(!$callback){
            throw new RouteNotFoundException();
        }

        if(is_callable($callback)){
            return $callback($request);
        }

        [$className, $method] = $callback;
        return call_user_func([new $className(), $method]);
    }
}