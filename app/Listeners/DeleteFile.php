<?php

namespace App\Listeners;

use App\Events\FileDeleting;
use Illuminate\Support\Facades\Storage;

class DeleteFile
{

    public function handle(FileDeleting $event): void
    {
        if (Storage::disk($event->file->disk)->exists($event->file->path)) {
            Storage::disk($event->file->disk)->delete($event->file->path);
        }
    }
}
