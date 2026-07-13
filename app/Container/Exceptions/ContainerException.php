<?php

declare(strict_types=1);

namespace SchoolERP\Container\Exceptions;

use RuntimeException;

/**
 * Thrown when the dependency injection container
 * fails to resolve a service.
 */
class ContainerException extends RuntimeException
{
}