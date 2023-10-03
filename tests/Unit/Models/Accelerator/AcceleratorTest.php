<?php

namespace Tests\Unit\Models\Accelerator;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 * @group models_accelerator
 */
class AcceleratorTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->clearTestDirectory();
        parent::tearDown();
    }

    public function testEventDeleteFile()
    {
        $user = User::createVerified();
        $accelerator = Accelerator::createFull($user);
        $file = $accelerator->files->first();

        $accelerator->delete();
        $this->assertFalse(Storage::disk($file->disk)->exists($file->path));
    }
}
