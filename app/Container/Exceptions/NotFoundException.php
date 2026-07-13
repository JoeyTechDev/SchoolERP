<?php

declare(strict_types=1);

namespace SchoolERP\Container\Exceptions;

use OutOfBoundsException;

/**
 * Thrown when a requested service
 * cannot be found inside the container.
 */
class NotFoundException extends OutOfBoundsException
{
}