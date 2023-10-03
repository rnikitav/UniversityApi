<?php

namespace App\Http\Requests\Accelerator;

use App\Rules\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => Helpers::$requiredString255,
            'description' => 'nullable|string',
            'published_at' => 'nullable|date_format:Y-m-d',
            'date_end_accepting' => Helpers::$requiredDate,
            'date_end' => Helpers::$requiredDate,
            'control_points' => Helpers::$requiredArray,
            'control_points.*.name' => Helpers::$requiredString255,
            'control_points.*.date_completion' => Helpers::$filledDate,
            'control_points.*.days_completion' => 'integer|required_without:control_points.*.date_completion',
            'control_points.*.max_score' => 'required|integer|max:65535',
            'files' => Helpers::$filledArray,
            'files.*' => Helpers::$requiredFile20mb
        ];
    }
}

