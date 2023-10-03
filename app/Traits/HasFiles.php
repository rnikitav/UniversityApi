<?php

namespace App\Traits;

use App\Events\FileDeleting;
use App\Models\File;
use App\Services\SaveFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFiles
{
    protected array $attachments = [];

    public static function bootHasFiles(): void
    {
        static::saved(function (Model $model) {
            SaveFiles::save($model);
        });
        static::deleted(function ($model) {
            foreach ($model->files as $file) {
                FileDeleting::dispatch($file);
                $file->delete();
            }
        });
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'owner');
    }

    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
