<?php

declare(strict_types=1);

use Fawkes\App;
use Fawkes\Config;
use Fawkes\Container;
use Fawkes\Controllers\HomeController;
use Fawkes\Controllers\UsersController;
use Fawkes\Enums\HttpMethod;
use Fawkes\Interfaces\UsersServiceInterface;
use Fawkes\Network\Request;
use Fawkes\Network\Router;
use Fawkes\Services\UsersService;

$root = __DIR__ . DIRECTORY_SEPARATOR;

include($root . 'vendor/autoload.php');

define('APP_PATH', $root . 'src' . DIRECTORY_SEPARATOR); 
define('VIEW_PATH', APP_PATH . 'Views' . DIRECTORY_SEPARATOR);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = Container::init();
$router    = new Router($container);
$request   = Request::capture(); 
$config    = new Config($_ENV);

$container->bind(UsersServiceInterface::class, UsersService::class);

$router->register(HttpMethod::GET, '/users', [UsersController::class, 'index']);

$router->registerRoutesFromControllerAttributes(...[HomeController::class]);

(new App($router, $request, $config))->run();