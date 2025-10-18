<?php

declare (strict_types=1);

namespace App\Form;

enum Role: string
{
case ADMIN = 'ROLE_ADMIN';
case USER = 'ROLE_USER';
}
