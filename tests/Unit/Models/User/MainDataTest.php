<?php


namespace Tests\Unit\Models\User;

use App\Models\User\UserMainData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 */
class MainDataTest extends TestCase
{
    use RefreshDatabase;

    public function testUserRelation()
    {
        $user = User::createVerified();
        /** @var UserMainData $mainData */
        $mainData = $user->mainData()->create();

        $this->assertTrue($mainData->user->is($user));
    }
}
