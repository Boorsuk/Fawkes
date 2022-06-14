<?php

declare(strict_types = 1);

namespace Fawkes\Exceptions;

class RouteNotFoundException extends \Exception
{
    protected $message = 'Route not found';
}