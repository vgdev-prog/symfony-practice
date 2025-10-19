<?php

namespace App\Exception;

class FileUploadException extends AbstractDomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getDomainError(): string
    {
        return ErrorCode::FILE_UPLOAD_FAILED->value;
    }

    public function getHttpStatusCode():int
    {
        return 500;
    }
}
