<?php

namespace App\Http\Requests\News;

use App\DTO\FilesDTO;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;


class CommonRequestHandler extends FormRequest
{
    private const DICTIONARY_FILE_CATEGORY = [
        'img_preview' => 'preview'
    ];
    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        foreach ($data as $key => $value){
            if ($value instanceof UploadedFile){
                $category = self::DICTIONARY_FILE_CATEGORY[$key]?? 'attachments';
                $data['files'][] = FilesDTO::createDtoFromArrayData($value, $category);
            }
        }
        return $data;
    }
}
