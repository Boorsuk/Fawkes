<?php

declare(strict_types = 1);

namespace Fawkes\Controllers;

use Fawkes\View;

class HomeController
{
    public function index(){
        $view = new View('home/index');

        return $view->render();
    }
}