<?php

namespace App\DTO;


use Illuminate\Http\UploadedFile;

class FilesDTO
{
    public UploadedFile $file;
    public string $category;


    public function __construct(UploadedFile $file, string $category)
    {
        $this->file = $file;
        $this->category = $category;
    }

    public static function createDtoFromArrayData(UploadedFile $file, $key): FilesDTO
    {
        $category = 'attachments';
        if ($key && is_string($key)){
            $category = explode('_', $key)[1]?? 'attachments';
        }
        return new self($file, $category);

    }


}
