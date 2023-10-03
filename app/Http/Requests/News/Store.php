<?php

namespace App\Http\Requests\News;

use App\Rules\Helpers;

class Store extends CommonRequestHandler
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => Helpers::$requiredString255,
            'body' => Helpers::$requiredString,
            'img_preview' => Helpers::$requiredFile2mbWebP,
            'img' => Helpers::$requiredFile2mbWebP,
            'published_at' => Helpers::$filledDate,
        ];
    }
}
