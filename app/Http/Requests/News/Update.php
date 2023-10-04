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
            'img_preview' => Helpers::$sometimesFileImage2mb,
            'img' => Helpers::$sometimesFileImage2mb,
            'published_at' => Helpers::$filledDate,
        ];
    }
}
