<?php

namespace App\Http\Requests\Tags;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * @property array $image_collections
 */
class Create extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => Helpers::$requiredString255,
            'image_collections' => 'array|min:0',
        ];

        if (count($this->image_collections)) {
            $rules['image_collections.*.id'] = 'required|integer|exists:image_collections,id';
        }

        return $rules;
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['image_collections'] = Arr::pluck($this->image_collections, 'id');
        return $data;
    }

    protected function prepareForValidation(): void
    {
        if (is_null($this->image_collections)) {
            $this->image_collections = [];
        }
    }
}

