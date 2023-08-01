<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class OperationNotPermittedException extends HttpException
{
    public static int $statusCode = 403;

    public function __construct()
    {
        parent::__construct(
            static::$statusCode,
            __('exception.not_permitted')
        );
    }
}
