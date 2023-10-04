<?php

namespace App\DTO;


use Illuminate\Http\UploadedFile;

class FilesDTO
{
    public UploadedFile $file;
    public string $category;
    public string $disk;

    public function __construct(UploadedFile $file, string $category, string $disk = 'private')
    {
        $this->file = $file;
        $this->category = $category;
        $this->disk = $disk;
    }
}
