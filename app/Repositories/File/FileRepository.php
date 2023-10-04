<?php

namespace App\Repositories\File;

use App\Events\FileDeleting;
use App\Models\File as FileModel;
use App\Repositories\AbstractRepository;

class FileRepository extends AbstractRepository
{
    protected function getClassName(): string
    {
        return FileModel::class;
    }

    public function deleteAllOldFilesByCategory(FileModel $instance):void
    {
        $instance->owner->files()->where([
            ['category', $instance->category],
            ['id', '<>', $instance->id],
        ])->get()->each(function ($file) {
            FileDeleting::dispatch($file);
            $file->delete();
        });
    }

    public function byIdWithHashOr404(int $id, string $hash): FileModel
    {
        /** @var FileModel $file */
        $file = $this->byId($id);
        if (is_null($file) || $file->sha256 !== $hash) {
            abort(404);
        }

        return $file;
    }
}
