<?php

namespace App\Http\Controllers\File;

use App\Events\FileDeleting;
use App\Exceptions\OperationNotPermittedException;
use App\Http\Controllers\Controller;
use App\Repositories\File\FileRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    protected FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function download(int $id, string $hash): StreamedResponse
    {
        $file = $this->fileRepository->byIdWithHashOr404($id, $hash);

        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }

    public function destroy(int $id, string $hash): Response
    {
        $file = $this->fileRepository->byIdWithHashOr404($id, $hash);

        if (method_exists($file->owner, 'canDeleteFiles') && !$file->owner->canDeleteFiles()) {
            throw new OperationNotPermittedException();
        }

        $deleted = $file->delete();
        if ($deleted) {
            FileDeleting::dispatch($file);
        }

        return response(['status' => $deleted]);
    }
}
