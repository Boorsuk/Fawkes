<?php

declare(strict_types = 1);

namespace Fawkes\Network;

use Fawkes\Attributes\Route;
use Fawkes\Container;
use Fawkes\Enums\HttpMethod;
use Fawkes\Exceptions\RouteNotFoundException;
use Fawkes\Network\Request;

class Router
{
    protected array $routes = [];
    private Container $container;

    public function __construct(Container $container) {
        $this->container = $container;        
    }

    public function register(HttpMethod $method, string $uri, array|callable $callback) : self{

        $this->routes[$method->value][$uri] = $callback;
        return $this;
    }

    public function resolve(Request $request) : mixed{
        $method = $request->getMethod();
        $uri    = $request->getUri();

        $callback = $this->routes[$method->value][$uri] ?? null;
        
        if(!$callback){
            throw new RouteNotFoundException();
        }

        if(is_callable($callback)){
            return $callback($request);
        }

        [$className, $classMethod] = $callback;

        $classInstance = $this->container->get($className);
        /** @todo obsluga parametrow i przekazywanie requestu tylko jak metoda oczekuje Obieku Fawkes\Request */
        return call_user_func_array([$classInstance, $classMethod], [$request]);
    }

    public function registerRoutesFromControllerAttributes(string ...$controllers) : void
    {
        foreach ($controllers as $controller) {
            try {
                $reflectedController = new \ReflectionClass($controller);
            } catch (\ReflectionException) {
                // Moze jakis log ? 
                continue;
            }

            $controllerMethods = $reflectedController->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($controllerMethods as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    /** @var Route */
                    $route = $attribute->newInstance();

                    $this->register($route->method(), $route->uri(), [$controller, $method->getName()]);
                }
            }
        }

        return;
    }

    public function routes() : array{
        return $this->routes;
    }
}