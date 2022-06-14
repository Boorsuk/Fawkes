<?php

declare(strict_types = 1);

namespace Fawkes;

use Fawkes\Exceptions\ContainerException;
use Fawkes\Exceptions\DependencyNotFound;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private $entries = [];
    private static $instance = null;

    private function __construct() {}

    public static function init() : static {
        if(!static::$instance){
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function get(string $id) { 
        if($this->has($id)){
            $entry = $this->entries[$id];

            return $entry($this);
        }

        $resolvedEntry = $this->resolve($id);
        if($resolvedEntry){
            return $resolvedEntry;
        }

        throw new DependencyNotFound('Missing entry for: '.$id);
    }

    public function has(string $id): bool { 
        return isset($this->entries[$id]);
    }

    public function bind(string $id, callable $concrete): void {
        $this->entries[$id] = $concrete;
    }

    public function resolve(string $id) {
        // 1. get class by reflection
        try {
            $reflectionClass = new \ReflectionClass($id);
        } catch (\ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }

        // 2. check if is instantiable
        if(!$reflectionClass->isInstantiable()){
            throw new DependencyNotFound("Missing binding for interface ${id}");
        }

        // 3. get constructor and it's parameters
        $constructor = $reflectionClass->getConstructor();

        /** class dont have any dependencies */
        if(!$constructor){
            return new $id();
        }

        // 4. resolve inner dependencies
        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $reflectionType = $parameter->getType();

            if(!$reflectionType){
                throw new ContainerException("Missing type hinting in ${id}");
            }
            
            if($reflectionType->isBuiltin()){
                if(!$parameter->isDefaultValueAvailable()){
                    throw new ContainerException("found builtin type: ".$reflectionType->getName()." without default value in ${id}");    
                }

                $dependencies[] = $parameter->getDefaultValue();
                continue;
            }

            $dependencies[] = $this->get($reflectionType->getName());
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}