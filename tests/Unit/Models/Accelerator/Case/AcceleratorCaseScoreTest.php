<?php

namespace Tests\Unit\Models\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseScore;
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
class AcceleratorCaseScoreTest extends TestCase
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
        $accelerator = AcceleratorGenerator::create($user);
        $case = AcceleratorCaseGenerator::createWithScore($accelerator);
        /** @var AcceleratorCaseScore $score */
        $score = $case->scores->first();

        $this->assertNotNull($score);
        $this->assertTrue($score->case->is($case));
        $this->assertTrue($score->user->is($user));
    }
}
