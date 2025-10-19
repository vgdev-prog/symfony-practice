<?php
declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

abstract class AbstractDomainException extends RuntimeException
{
    abstract public function getDomainError(): string;

    public function getHttpStatusCode(): int
    {
        return 400;
    }
}
