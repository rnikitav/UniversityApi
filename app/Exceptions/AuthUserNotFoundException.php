<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthUserNotFoundException extends HttpException
{
    private static int $statusCode = 422;

    public function __construct()
    {
        parent::__construct(static::$statusCode,  __('auth.user_not_found'));
    }
}
