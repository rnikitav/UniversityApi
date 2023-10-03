<?php

namespace App\Services;

use App\DTO\FilesDTO;
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
        $disk = 'private';
        $fileService = (new FileService($model))->disk($disk);
        $saved = [];
        $attachments = method_exists($model, 'getAttachments') ? $model->getAttachments() : [];
        foreach ($attachments as $attachment) {
            try {
                if ($attachment instanceof UploadedFile) {
                    $saved[] = $fileService->save($attachment);
                } elseif ($attachment instanceof FilesDTO) {
                    $fileService->category($attachment->category);
                    $saved[] = $fileService->save($attachment->file);
                }
            } catch (Exception $exception) {
                static::deleteSaved($saved, $disk);
                throw $exception;
            }
        }
    }

    protected static function deleteSaved(array $saved, string $disk): void
    {
        foreach ($saved as $path) {
            Storage::disk($disk)->delete($path);
        }
    }
}
