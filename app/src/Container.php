<?php

declare(strict_types = 1);

namespace Fawkes;

use Fawkes\Exceptions\ContainerException;
use Fawkes\Exceptions\DependencyNotFound;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    private $entries = [];

    public function get(string $id) { 
        if($this->has($id)){
            $entry = $this->entries[$id];

            return $entry($this);
        }

        throw new DependencyNotFound('Missing entry for: '.$id);
    }

    public function has(string $id): bool { 
        return isset($this->entries[$id]);
    }

    public function bind(string $id, callable $concrete): void {
        $this->entries[$id] = $concrete;
    }
}