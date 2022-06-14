<?php

declare(strict_types = 1);

namespace Fawkes;

final class View
{
    private string $path;
    private array $params;

    public function __construct(string $path, array $params = []){
        $this->path   = VIEW_PATH . $path . '.php';
        $this->params = $params;
    }

    public function render() : string{
        ob_start();

        foreach ($this->params as $key => $value) {
            $$key = $value;
        }

        include $this->path;

        return ob_get_clean();
    }

    public function __get(string $name){
        return $this->params[$name] ?? null;
    }
}