<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseSolutionStatus;
use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateSolutionStatus extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => Helpers::$requiredString .'|exists:accelerator_case_solution_statuses,id',
            'score' => 'integer|min:1|required_if:status,' . AcceleratorCaseSolutionStatus::approved(),
            'message' => Helpers::$filledString,
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));

        $data['status_id'] = Arr::pull($data, 'status');
        if ($data['status_id'] != AcceleratorCaseSolutionStatus::approved()) {
            Arr::forget($data, 'score');
        }

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

