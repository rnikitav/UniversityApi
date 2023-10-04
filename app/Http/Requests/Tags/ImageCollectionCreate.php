<?php

namespace App\Http\Requests\Tags;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array $files
 */
class ImageCollectionCreate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => Helpers::$requiredString255,
            'files' => Helpers::$filledArray,
            'files.*' => Helpers::$requiredFileImage2mb
        ];
    }

    public function prepareData(): array
    {
        return $this->only(UtilsHelpers::keysRules($this));
    }
}

