<?php

declare(strict_types = 1);

namespace Fawkes;

use Fawkes\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    private $entries = [];

    public function get(string $id) { 

    }

    public function has(string $id): bool { 
        return isset($this->entries[$id]);
    }

    public function bind(string $id): void {
        // get reflection
        try {
            $reflectionClass = new ReflectionClass($id);
        } catch (\ReflectionException $e) {
            throw new ContainerException($e->getMessage(), $e->getCode());
        }
        
        


        // check if is instanciable

        // check if has consturctor and if has then take the parameters

        // resolve costructor parameters
    }
}