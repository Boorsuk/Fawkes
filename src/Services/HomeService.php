<?php

declare(strict_types = 1);

namespace Fawkes\Services;

use Fawkes\Models\HomeModel;

class HomeService
{
    private HomeModel $homeModel;

    public function __construct(HomeModel $homeModel) {
        $this->homeModel = $homeModel;
    }

    public function fetchHomeData() : string{
        return $this->homeModel->returnHelloWorld();
    }
}