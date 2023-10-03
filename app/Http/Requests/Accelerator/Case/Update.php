<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => Helpers::$filledString255,
            'description' => Helpers::$filledString,
            'participation' => Helpers::$filledString .'|exists:accelerator_case_participations,id',
            'files' => Helpers::$filledArray,
            'files.*' => Helpers::$requiredFile20mb
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));

        if (array_key_exists('participation', $data)) {
            $data['participation_id'] = Arr::pull($data, 'participation');
        }

        return $data;
    }
}

