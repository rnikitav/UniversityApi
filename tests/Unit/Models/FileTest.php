<?php

namespace Tests\Unit\Models;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group models
 */
class FileTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->clearTestDirectory();
        parent::tearDown();
    }

    public function testOwnerRelation()
    {
        $user = User::createVerified();
        $accelerator = Accelerator::createFull($user);
        /** @var File $file */
        $file = $accelerator->files->first();

        $this->assertTrue($file->owner->is($accelerator));

        $accelerator->delete();
        $this->assertFalse(Storage::exists($file->path));
    }
}
