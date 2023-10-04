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
            'img_preview' => Helpers::$requiredFileImage2mb,
            'img' => Helpers::$requiredFileImage2mb,
            'published_at' => Helpers::$filledDate,
        ];
    }
}
