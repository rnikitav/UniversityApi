<?php

namespace Tests\Unit\Utils;

use App\Utils\Resource;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * @group unit
 * @group utils
 */
class ResourceTest extends TestCase
{
    public function testFormatDate()
    {
        $expected = '2022-03-23';

        $this->assertEquals($expected, Resource::formatDate(Carbon::parse($expected)));
    }

    public function testFormatDateTime()
    {
        $expected = '2022-03-23 13:58:45';

        $this->assertEquals($expected, Resource::formatDateTime(Carbon::parse($expected)));
    }
}
