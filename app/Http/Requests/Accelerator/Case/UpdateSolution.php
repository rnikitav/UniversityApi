<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSolution extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => Helpers::$requiredString,
            'files' => Helpers::$filledArray,
            'files.*' => Helpers::$requiredFile20mb
        ];
    }

    public function prepareData(): array
    {
        return $this->only(UtilsHelpers::keysRules($this));
    }
}

