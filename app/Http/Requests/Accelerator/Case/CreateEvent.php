<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateEvent extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => Helpers::$requiredString,
            'type' => Helpers::$requiredString .'|exists:accelerator_case_event_types,id',
            'participant' => 'filled|integer|exists:users,id',
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['initializer_id'] = $this->user()?->id;
        $data['type_id'] = Arr::pull($data, 'type');
        if (array_key_exists('participant', $data)) {
            $data['participant_id'] = Arr::pull($data, 'participant');
        } else {
            $data['participant_id'] = $data['initializer_id'];
        }
        return $data;
    }
}

