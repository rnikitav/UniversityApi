<?php

namespace App\Services\File;

use App\Exceptions\Inner\InvalidDatabaseSetException;
use App\Exceptions\Inner\InvalidDataSetException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    private Model $model;
    private string $disk;
    private string $category = 'attachments';
    private string $directory;
    private string $attribute = 'files';

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->disk = config('filesystems.default');

        $this->directory = Str::lower(class_basename($model));
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    public function category(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function directory(string $directory): self
    {
        $this->directory = $directory;
        return $this;
    }

    public function modelAttribute(string $attribute): self
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @throws InvalidDataSetException|InvalidDatabaseSetException
     */
    public function save(UploadedFile $file, string $as = null): string
    {
        $to = $this->directory . '/' . $this->model->{$this->model->getKeyName()};
        if (Str::endsWith($to, '/')) {
            $to = preg_replace('/\/$/', '', $to);
        }

        $fileName = $as ?? Str::uuid() . '.' .$file->getClientOriginalExtension();
        $path = $file->storeAs($to, $fileName, $this->disk);

        if (!$path) {
            throw InvalidDataSetException::instance(
                'Ошибка при записи файла.',
                ['path' => $to, 'file' => $fileName, 'disk' => $this->disk]
            );
        }

        try {
            $fileData = [
                'disk' => $this->disk,
                'category' => $this->category,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'sha256' => hash_file('sha256', $file->path())
            ];

             $this->attach($fileData);
        } catch (Exception $exception) {
            Storage::disk($this->disk)->delete($path);
            throw new InvalidDatabaseSetException($exception->getMessage());
        }

        return $path;
    }

    private function attach(array $fileData): void
    {
        if (!(method_exists($this->model, $this->attribute)
            && $this->model->{$this->attribute}() instanceof MorphMany
        )) {
            throw InvalidDataSetException::instance(
                sprintf('Ошибка при записи файла. У модели отсутствует аттрибут %s для сохранения данных.', $this->attribute)
            );
        }

        $newFile = $this->model->{$this->attribute}()->create($fileData);
        $this->model->{$this->attribute}->add($newFile);
    }
}
