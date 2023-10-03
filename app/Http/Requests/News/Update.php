<?php

namespace App\Http\Requests\News;

use App\Rules\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules()
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
