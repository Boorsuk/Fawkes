<?php

declare(strict_types=1);

use Fawkes\App;
use Fawkes\Config;
use Fawkes\Container;
use Fawkes\Controllers\HomeController;
use Fawkes\Controllers\UsersController;
use Fawkes\Models\HomeModel;
use Fawkes\Network\Request;
use Fawkes\Network\Router;
use Fawkes\Services\HomeService;

$root = __DIR__ . DIRECTORY_SEPARATOR;

include($root . 'vendor/autoload.php');

define('APP_PATH', $root . 'src' . DIRECTORY_SEPARATOR); 
define('VIEW_PATH', APP_PATH . 'Views' . DIRECTORY_SEPARATOR);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = Container::init();
$router    = new Router($container);
$request   = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); 
$config    = new Config($_ENV);

$router->register('GET', '/', [HomeController::class, 'index'])
       ->register('GET', '/users', [UsersController::class, 'index']);

(new App($router, $request, $config))->run();