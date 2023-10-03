<?php

namespace Tests\Unit\Models\Accelerator\Case;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\Accelerator\AcceleratorCase;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 * @group models_accelerator_case
 */
class AcceleratorCaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function tearDown(): void
    {
        $this->clearTestDirectory();
        parent::tearDown();
    }

    public function testEventDeleteFile()
    {
        $user = User::createVerified();
        $accelerator = Accelerator::create($user);
        $case = AcceleratorCase::createWithFile($accelerator);
        $file = $case->files->first();

        $case->delete();
        $this->assertFalse(Storage::disk($file->disk)->exists($file->path));
    }
}
