<?php

namespace App\Http\Requests\User;

use App\Rules\Helpers;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Update extends AbstractUserRequest
{
    public function rules(Request $request): array
    {
        return [
            'email' => ['filled','email', Rule::unique('users', 'login')->ignore($request->route('id'))],
            'password' => ['filled', 'confirmed', new PasswordRule()],
            'first_name' => Helpers::$filledString255,
            'last_name' => Helpers::$filledString255,
            'patronymic' => Helpers::$filledString255,
            'roles' => 'filled|array',
            'roles.*.id' => 'required|exists:roles,id'
        ];
    }
}

