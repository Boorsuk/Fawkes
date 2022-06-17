<?php

declare(strict_types = 1);

namespace Fawkes\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class MissingTypeHintException extends \Exception implements ContainerExceptionInterface
{

}