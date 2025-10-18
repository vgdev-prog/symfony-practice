<?php

declare (strict_types=1);

namespace App\Exception;

enum ErrorCode:string
{
    case USER_ALREADY_EXISTS = 'USER_ALREADY_EXISTS';
}
