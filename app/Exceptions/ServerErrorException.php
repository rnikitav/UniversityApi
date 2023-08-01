<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ServerErrorException extends HttpException
{
    public static int $statusCode = 500;

    public function __construct()
    {
        parent::__construct(
            static::$statusCode,
            __('exception.server_error')
        );
    }
}
