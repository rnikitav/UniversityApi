<?php

namespace App\Http\Requests\News;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $title
 * @property string $body
 * @property string $desc
 * @property string $img
 * @property Carbon $created_at
 */
class Store extends FormRequest
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
            'title' => ['required' , 'min:5'],
            'body' => ['required'],
            'img_preview' => ['required'],
            'img' => ['required'],
            'published_at' => ['sometimes', 'date'],
        ];
    }
}
