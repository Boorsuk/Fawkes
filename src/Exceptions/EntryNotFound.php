<?php

declare(strict_types = 1);

namespace Fawkes\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class EntryNotFound extends \Exception implements NotFoundExceptionInterface
{
    
}