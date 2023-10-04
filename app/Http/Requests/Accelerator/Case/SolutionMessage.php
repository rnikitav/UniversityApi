<?php

namespace App\Http\Requests\Accelerator\Case;

use App\Rules\Helpers;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $message
 */
class SolutionMessage extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => Helpers::$requiredString,
        ];
    }

    public function prepareData(): array
    {
        $data['messages'][] = [
            'message' => $this->message,
            'user_id' => $this->user()?->id
        ];
        return $data;
    }
}

