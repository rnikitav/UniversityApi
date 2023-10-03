<?php

namespace App\Services;

use App\Services\File\FileService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SaveFiles
{
    /**
     * @throws Exception
     */
    public static function save(Model $model): void
    {
        $fileService = (new FileService($model))->disk('private');
        $saved = [];
        $attachments = $model->attachments ?? [];
        foreach ($attachments as $attachment) {
            if ($attachment instanceof UploadedFile) {
                try {
                    $saved[] = $fileService->save($attachment);
                } catch (Exception $exception) {
                    static::deleteSaved($saved);
                    throw $exception;
                }
            }
        }
    }

    protected static function deleteSaved(array $saved): void
    {
        foreach ($saved as $path) {
            Storage::delete($path);
        }
    }
}
