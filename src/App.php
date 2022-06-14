<?php

declare(strict_types = 1);

namespace Fawkes;

use Fawkes\Network\Request;
use Fawkes\Network\Router;
use Fawkes\Config;
use Fawkes\Database\Database;

class App
{
    private Router $router;
    private Request $request;
    private Config $config;

    private static Database $db;

    public function __construct(Router $router, Request $request, Config $config){
        $this->router  = $router;
        $this->request = $request;
        $this->config  = $config;

        static::$db = new Database($config->getDatabaseConfig());
    }

    public static function getDatabase() : Database{
        return static::$db;
    }

    public function run() : void{
        echo $this->router->resolve($this->request);
        exit;
    }
}