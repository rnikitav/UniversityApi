<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateSolution extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'control_point' => 'required|integer|exists:accelerator_control_points,id',
            'description' => Helpers::$requiredString,
            'files' => Helpers::$filledArray,
            'files.*' => Helpers::$requiredFile20mb
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['author_id'] = $this->user()?->id;
        $data['control_point_id'] = Arr::pull($data, 'control_point');
        return $data;
    }
}

