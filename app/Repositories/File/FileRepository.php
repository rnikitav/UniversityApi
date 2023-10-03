<?php

namespace App\Repositories\File;

use App\Events\FileDeleting;
use App\Models\File as FileModel;

class FileRepository
{
    public function deleteAllOldFilesByOwnerOwnerIdCategory(FileModel $instance):void
    {
        $collection = $instance->owner->files()->where('category', $instance->category)
            ->orderBy('id', 'desc')->get();

        $firstElement = $collection->shift();
        $filePath = $firstElement->path;
        foreach ($collection as $file) {
            if ($file->path !== $filePath){
                //TODO доделать когда файлы одинаковое имя
                FileDeleting::dispatch($file);
            }
            $file->delete();
        }
    }
}
