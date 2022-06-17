<?php

declare(strict_types = 1);

namespace Fawkes\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class BuiltinTypeOccuredException extends \Exception implements ContainerExceptionInterface
{

}