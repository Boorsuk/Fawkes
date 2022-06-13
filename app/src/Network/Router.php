<?php

declare(strict_types = 1);

namespace Fawkes\Network;

use Fawkes\Container;
use Fawkes\Exceptions\RouteNotFoundException;
use Fawkes\Network\Request;
use Psr\Container\NotFoundExceptionInterface;

class Router
{
    protected array $routes = [];
    private Container $container;

    public function __construct(Container $container) {
        $this->container = $container;        
    }

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

        try {
            $classInstance = $this->container->get($className);
        } catch (NotFoundExceptionInterface) {
            $classInstance = new $className();    
        }

        return call_user_func([$classInstance, $method]);
    }

    public function routes() : array{
        return $this->routes;
    }
}