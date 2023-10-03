<?php

namespace App\Http\Requests\News;

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
            'title' => ['sometimes', 'min:5'],
            'body' => ['sometimes'],
            'img_preview' => ['sometimes'],
            'img' => ['sometimes'],
            'published_at' => ['sometimes', 'date'],
        ];
    }
}
