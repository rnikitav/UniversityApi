<?php

namespace App\Http\Requests\User;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request): array
    {
        return [
            'email' => ['filled','email','max:255', Rule::unique('users')->ignore($request->route('id'))],
            'password' => ['filled', 'confirmed', new PasswordRule()],
            'roles' => 'filled|array',
            'roles.*.id' => 'required|exists:roles,id'
        ];
    }
}

