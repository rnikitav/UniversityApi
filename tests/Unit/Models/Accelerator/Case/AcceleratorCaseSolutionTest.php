<?php

namespace Tests\Unit\Models\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseSolution;
use App\Models\Accelerator\Case\AcceleratorCaseSolutionStatus;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\Accelerator\Accelerator as AcceleratorGenerator;
use Tests\Generators\Accelerator\AcceleratorCase as AcceleratorCaseGenerator;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 */
class AcceleratorCaseSolutionTest extends TestCase
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
        $accelerator = AcceleratorGenerator::createWithControlPoint($user);
        $point = $accelerator->controlPoints->first();
        $case = AcceleratorCaseGenerator::createWithSolution($accelerator, $point);
        /** @var AcceleratorCaseSolution $solution */
        $solution = $case->solutions->first();

        $this->assertNotNull($solution);
        $this->assertTrue($solution->case->is($case));
        $this->assertTrue($solution->author->is($user));
        $this->assertEquals($solution->status->id, AcceleratorCaseSolutionStatus::submitted());
        $this->assertTrue($solution->controlPoint->is($point));
    }
}
