<?php

namespace App\Traits;

use App\Models\File;
use App\Repositories\File\FileRepository;
use Illuminate\Support\Facades\DB;

trait DeleteOldFiles
{
    protected array $attachments = [];

    public static function bootDeleteOldFiles(): void
    {
        static::saved(function (File $model) {
            DB::afterCommit(fn() => $model->deleteFiles());
        });
    }

    public function deleteFiles(): void
    {
        $model = $this->owner;
        if (method_exists($model, 'needDeleteOldFiles') && $model->needDeleteOldFiles()) {
            /** @var FileRepository $repository */
            $repository = app(FileRepository::class);
            $repository->deleteAllOldFilesByOwnerOwnerIdCategory($this);
        }
    }
}
