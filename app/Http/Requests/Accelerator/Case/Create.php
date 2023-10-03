<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * @property array $files
 */
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
            'description' => Helpers::$requiredString,
            'participation' => Helpers::$requiredString .'|exists:accelerator_case_participations,id',
            'files' => Helpers::$filledArray,
            'files.*' => Helpers::$requiredFile20mb
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['participation_id'] = Arr::pull($data, 'participation');
        return $data;
    }
}

