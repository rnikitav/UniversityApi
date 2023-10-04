<?php

namespace App\Http\Requests\News;

use App\DTO\FilesDTO;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;


class CommonRequestHandler extends FormRequest
{
    private const DICTIONARY_FILE_CATEGORY = [
        'img_preview' => 'preview',
        'img' => 'main',
    ];

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['files'] = [];
        foreach ($data as $key => $value){
            if ($value instanceof UploadedFile && array_key_exists($key, self::DICTIONARY_FILE_CATEGORY)){
                $category = self::DICTIONARY_FILE_CATEGORY[$key];
                $data['files'][] = new FilesDTO($value, $category, 'public');
            }
        }
        return $data;
    }
}
