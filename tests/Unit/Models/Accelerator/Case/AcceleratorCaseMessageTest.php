<?php

namespace Tests\Unit\Models\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\Accelerator\AcceleratorCase;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 */
class AcceleratorCaseMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testRelations()
    {
        $user = User::createVerified();
        $accelerator = Accelerator::create($user);
        $case = AcceleratorCase::create($accelerator);
        /** @var AcceleratorCaseMessage $message */
        $message = $case->messages()->create(['user_id' => $user->id, 'message' => 'test']);

        $this->assertNotNull($message);
        $this->assertTrue($message->owner->is($case));
        $this->assertTrue($message->user->is($user));
    }
}
