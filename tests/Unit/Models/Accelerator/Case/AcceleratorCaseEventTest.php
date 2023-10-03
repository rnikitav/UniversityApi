<?php

namespace Tests\Unit\Models\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseEvent;
use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;
use App\Models\Accelerator\Case\AcceleratorCaseEventType;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\Accelerator\AcceleratorCase;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 */
class AcceleratorCaseEventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testRelations()
    {
        $user = User::first();
        $accelerator = Accelerator::create($user);
        $case = AcceleratorCase::createWithEvent($accelerator);
        /** @var AcceleratorCaseEvent $event */
        $event = $case->events->first();
        $event->update(['moderator_id' => $user->id]);
        $event->refresh();

        $this->assertNotNull($event);
        $this->assertTrue($event->case->is($case));
        $this->assertTrue($event->initializer->is($user));
        $this->assertEquals($event->type->id, AcceleratorCaseEventType::enter());
        $this->assertTrue($event->participant->is($user));
        $this->assertEquals($event->status->id, AcceleratorCaseEventStatus::submitted());
        $this->assertTrue($event->moderator->is($user));
    }
}
