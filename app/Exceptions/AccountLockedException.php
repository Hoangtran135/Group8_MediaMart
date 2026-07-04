<?php

namespace App\Exceptions;

use Exception;

class AccountLockedException extends Exception
{
    public function __construct(string $message = 'Tài khoản của bạn đã bị khóa.')
    {
        parent::__construct($message);
    }
}
