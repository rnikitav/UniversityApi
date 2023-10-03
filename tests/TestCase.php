<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function clearTestDirectory(): void
    {
        $storage = Storage::disk(config('filesystems.default'));

        foreach ($storage->directories() as $directory) {
            $storage->deleteDirectory($directory);
        }
        $storage->delete(Arr::except($storage->allFiles(), '.gitignore'));
    }
}
