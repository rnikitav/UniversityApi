<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthUserNotVerifiedException extends HttpException
{
    public static int $statusCode = 403;

    public function __construct(string $email)
    {
        parent::__construct(
            static::$statusCode,
            __('auth.email_not_verified', ['email' => $email])
        );
    }
}
