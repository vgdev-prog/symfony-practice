<?php

declare (strict_types=1);

namespace App\Exception;

enum ErrorCode:string
{
    /*
     * User
     * */
    case USER_ALREADY_EXISTS = 'USER_ALREADY_EXISTS';


    /*
     * File
     * */
    case FILE_UPLOAD_FAILED = 'FILE_UPLOAD_FAILED';


}
