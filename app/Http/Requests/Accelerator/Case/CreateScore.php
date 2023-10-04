<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateScore extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'score' => 'required|integer|min:1',
            'message' => Helpers::$filledString,
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['user_id'] = $this->user()?->id;
        $data['messages'] = [];
        if (array_key_exists('message', $data) && $data['message']) {
            $data['messages'][] = [
                'message' => $data['message'],
                'user_id' => $this->user()?->id
            ];
        }
        return $data;
    }
}

