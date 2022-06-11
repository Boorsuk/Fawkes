<?php

declare(strict_types = 1);

namespace Fawkes\Exceptions;

use Exception;

class RouteNotFoundException extends Exception
{
    protected $message = 'Route not found';
}