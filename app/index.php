<?php

declare(strict_types=1);

use Fawkes\App;
use Fawkes\Config;
use Fawkes\Controllers\HomeController;
use Fawkes\Network\Request;
use Fawkes\Network\Router;

$root = __DIR__ . DIRECTORY_SEPARATOR;

include($root . 'vendor/autoload.php');

define('APP_PATH', $root . 'src' . DIRECTORY_SEPARATOR); 
define('VIEW_PATH', APP_PATH . 'Views' . DIRECTORY_SEPARATOR);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router  = new Router();
$request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); 
$config  = new Config($_ENV);

$router->register('GET',  '/', [HomeController::class, 'index'])
       ->register('POST', '/transactions', [HomeController::class, 'uploadTransactions'])
       ->register('GET',  '/transactions', [HomeController::class, 'viewTransactions']);

(new App($router, $request, $config))->run();