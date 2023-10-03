<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateStatus extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => Helpers::$filledString,
            'status' => Helpers::$requiredString .'|exists:accelerator_case_statuses,id',
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['status_id'] = Arr::pull($data, 'status');
        $data['messages'] = [];

        if (array_key_exists('message', $data) && $data['message']) {
            $data['messages'][] = [
                'user_id' => $this->user()->id,
                'message' => Arr::pull($data, 'message')
            ];
        }

        return $data;
    }
}

