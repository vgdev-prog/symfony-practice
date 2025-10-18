<?php

declare (strict_types=1);

namespace App\Exception;

use RuntimeException;

class UserAlreadyExistException extends RuntimeException
{
    public function __construct(
        private readonly string $userEmail,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            "User with email '{$this->userEmail}'
  already exists",
            400,
            $previous
        );
    }

    public function getDomainError()
    {
        return ErrorCode::USER_ALREADY_EXISTS->value;
    }

}
