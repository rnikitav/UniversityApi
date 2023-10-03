<?php

namespace Tests\Unit\Models\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseParticipant;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\Accelerator\AcceleratorCase;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 */
class AcceleratorCaseParticipantTest extends TestCase
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
        /** @var AcceleratorCaseParticipant $participant */
        $participant = $case->participants->first();

        $this->assertNotNull($participant);
        $this->assertTrue($participant->case->is($case));
        $this->assertTrue($participant->user->is($user));
        $this->assertEquals($participant->role->id, AcceleratorCaseRole::owner());
    }
}
