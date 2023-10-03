<?php

namespace App\Utils;

use App\Exceptions\Inner\InvalidDatabaseSetException;
use App\Exceptions\Inner\InvalidDataSetException;
use App\Exceptions\ServerErrorException;
use Closure;
use Exception;
use Illuminate\Support\Facades\DB as DBFacade;
use Illuminate\Support\Facades\Log;
use Throwable;

class DB
{
    /**
     * @throws Throwable
     */
    public static function inTransaction(Closure $callback)
    {
        DBFacade::beginTransaction();

        try {
            $result = $callback();

            DBFacade::commit();

        } catch (Exception $exception) {

            DBFacade::rollBack();

            $channel = $exception instanceof InvalidDatabaseSetException ? 'database' : config('logging.default');
            $data = $exception instanceof InvalidDataSetException ? $exception->getData() : [];
            Log::channel($channel)->error($exception->getMessage(), $data);

            throw new ServerErrorException();
        }

        return $result;
    }
}
