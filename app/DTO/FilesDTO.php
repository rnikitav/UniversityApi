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

    public static function createDtoFromArrayData(UploadedFile $file, $category): FilesDTO
    {
        return new self($file, $category);

    }


}
