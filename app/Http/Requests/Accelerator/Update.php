<?php

namespace App\Http\Requests\Accelerator;

use App\Rules\Helpers;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array $control_points
 */
class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'published_at' => Helpers::$filledDate,
            'date_end_accepting' => Helpers::$filledDate,
            'date_end' => Helpers::$filledDate,
            'control_points' => Helpers::$filledArray,
            'control_points.*.id' => 'required|integer|exists:accelerator_control_points,id',
            'control_points.*.date_completion' => Helpers::$requiredDate,
        ];
    }
}

