<?php

namespace App\Traits;

use App\DTO\FilesDTO;
use App\Events\FileDeleting;
use App\Models\File;
use App\Services\SaveFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;

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
        return $this->morphMany(File::class, 'file');
    }

    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }

    public function getAttachments(): array
    {
        if (empty($this->attachments)) {
            $this->prepareDtoFiles();
        }
        return $this->attachments;
    }

    private function prepareDtoFiles(): void
    {
        $files = request()->file();

        if (empty($files)) {
            return;
        }

        if ($files instanceof UploadedFile) {
            $this->attachments = $files;
            return;
        }

        if (is_iterable($files)) {
            foreach ($files as $uploadName => $file) {
                $this->attachments[] = FilesDTO::createDtoFromArrayData($file, $uploadName);
            }
        }
    }
}
