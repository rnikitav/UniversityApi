<?php

namespace Tests\Unit\Utils;

use App\Exceptions\Inner\InvalidDatabaseSetException;
use App\Exceptions\ServerErrorException;
use App\Utils\DB;
use Tests\TestCase;

/**
 * @group unit
 * @group utils
 */
class DBTest extends TestCase
{
    public function testInTransaction()
    {
        $expected = 'test';
        $callback = fn () => $expected;

        $this->assertEquals($expected, DB::inTransaction($callback));
    }

    public function testInTransactionException()
    {
        $callback = function () {
            throw new InvalidDatabaseSetException();
        };

        $this->expectException(ServerErrorException::class);
        DB::inTransaction($callback);
    }
}
