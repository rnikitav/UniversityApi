<?php

namespace App\Http\Requests\News;

use App\Rules\Helpers;

class Update extends CommonRequestHandler
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => Helpers::$filledString255,
            'body' => Helpers::$filledString,
            'img_preview' => Helpers::$sometimesFile2mbWebP,
            'img' => Helpers::$sometimesFile2mbWebP,
            'published_at' => Helpers::$filledDate,
        ];
    }
}
