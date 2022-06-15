<?php

declare(strict_types = 1);

namespace Fawkes\Controllers;

use Fawkes\Interfaces\UsersServiceInterface;

class UsersController
{
    private UsersServiceInterface $userService;

    public function __construct(UsersServiceInterface $userService){
        $this->userService = $userService;
    }

    public function index(){
        return $this->userService->returnHelloUser();
    }
}