<?php

declare(strict_types = 1);

namespace Fawkes\Controllers;

use Fawkes\Attributes\Route;
use Fawkes\Enums\HttpMethod;
use Fawkes\Network\Request;
use Fawkes\Services\HomeService;
use Fawkes\View;

class HomeController
{
    private HomeService $homeService;

    public function __construct(HomeService $homeService, int $sadge = 5) {
        $this->homeService = $homeService;
    }

    #[Route(HttpMethod::GET, '/')]
    public function index(Request $request){
        $msg = $this->homeService->fetchHomeData();
        $params = $request->params();

        $view = new View('home/index', ['msg' => $msg, 'params' => $params]);
        return $view->render();
    }
}