<?php

namespace App\Http\Requests\Permissions;

use Illuminate\Foundation\Http\FormRequest;

class PermissionEdit extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preview' => 'required|string|min:3|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'preview' => 'наименование для отображения',
        ];
    }
}

