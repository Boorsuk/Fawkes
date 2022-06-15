<?php

declare(strict_types = 1);

namespace Fawkes\Services;

use Fawkes\Interfaces\UsersServiceInterface;

class UsersService implements UsersServiceInterface
{

    public function returnHelloUser(): string {
        return 'Hello users';
    }
    
}