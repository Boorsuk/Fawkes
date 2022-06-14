<?php

declare(strict_types = 1);

namespace Fawkes\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class DependencyNotFound extends \Exception implements NotFoundExceptionInterface
{
    
}