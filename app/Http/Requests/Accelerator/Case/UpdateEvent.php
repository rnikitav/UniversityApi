<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;
use App\Rules\Helpers;
use App\Utils\Helpers as UtilsHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * @property string $status
 */
class UpdateEvent extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => Helpers::$requiredString .'|exists:accelerator_case_event_statuses,id|not_in:' . AcceleratorCaseEventStatus::submitted(),
            'new_owner' => 'filled|integer|exists:users,id',
        ];
    }

    public function prepareData(): array
    {
        $data = $this->only(UtilsHelpers::keysRules($this));
        $data['status_id'] = Arr::pull($data, 'status');
        $data['moderator_id'] = $this->user()?->id;
        return $data;
    }
}

