<?php

namespace Tests\Unit\Models\Accelerator;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 */
class AcceleratorControlPointTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->clearTestDirectory();
        parent::tearDown();
    }

    public function testAcceleratorRelation()
    {
        $user = User::createVerified();
        $accelerator = Accelerator::createFull($user);
        $point = $accelerator->controlPoints->first();

        $this->assertTrue($point->accelerator->is($accelerator));
    }
}
