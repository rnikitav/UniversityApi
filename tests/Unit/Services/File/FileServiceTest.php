<?php

namespace Tests\Unit\Services\File;

use App\Exceptions\Inner\InvalidDatabaseSetException;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\File;
use App\Services\File\FileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Generators\Accelerator\Accelerator;
use Tests\Generators\User;
use Tests\TestCase;

/**
 * @group unit
 * @group services
 */
class FileServiceTest extends TestCase
{
    use RefreshDatabase;

    private AcceleratorModel $accelerator;
    private UploadedFile $file;
    private string $defaultDisk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $user = User::createVerified();
        $this->accelerator = Accelerator::create($user);

        $this->file = UploadedFile::fake()->create('test.md');
        $this->defaultDisk = config('filesystems.default');
    }

    protected function tearDown(): void
    {
        $this->clearTestDirectory();
        parent::tearDown();
    }

    public function testSave()
    {
        $fileService = new FileService($this->accelerator);
        $path = $fileService->save($this->file);

        $this->assertTrue(Storage::disk($this->defaultDisk)->exists($path));
        $this->accelerator->refresh();

        /** @var File $acceleratorFile */
        $acceleratorFile = $this->accelerator->files->first();
        $this->assertNotNull($acceleratorFile);
        $this->assertEquals($this->defaultDisk, $acceleratorFile->disk);
        $this->assertEquals('attachments', $acceleratorFile->category);
        $this->assertEquals('accelerator/' . $this->accelerator->id . '/' . $this->file->getClientOriginalName(), $acceleratorFile->path);
    }

    public function testSaveWithOptions()
    {
        $fileService = (new FileService($this->accelerator))
            ->disk($this->defaultDisk)
            ->category('test')
            ->directory('accelerator_files')
            ->modelAttribute('files');
        $path = $fileService->save($this->file);

        $this->assertTrue(Storage::disk($this->defaultDisk)->exists($path));
        $this->accelerator->refresh();

        /** @var File $acceleratorFile */
        $acceleratorFile = $this->accelerator->files->first();
        $this->assertNotNull($acceleratorFile);
        $this->assertEquals($this->defaultDisk, $acceleratorFile->disk);
        $this->assertEquals('test', $acceleratorFile->category);
        $this->assertEquals('accelerator_files/' . $this->accelerator->id . '/' . $this->file->getClientOriginalName(), $acceleratorFile->path);
    }

    public function testIncorrectModelAttribute()
    {
        $fileService = (new FileService($this->accelerator))
            ->modelAttribute('files__');

        $this->expectException(InvalidDatabaseSetException::class);
        $fileService->save($this->file);
    }
}
