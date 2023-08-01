<?php

namespace App\Http\Requests\Permissions;

use Illuminate\Foundation\Http\FormRequest;

class RolesEdit extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'filled|string|min:3|max:255',
            'permissions' => 'filled|array',
            'permissions.*.name' => 'required|exists:permissions,name'
        ];
    }

}

