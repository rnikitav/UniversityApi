<?php

namespace App\Http\Requests\User;

use App\Rules\Helpers;
use App\Rules\PasswordRule;

/**
 * @property string $email
 * @property string $password
 * @property array $roles
 */
class Create extends AbstractUserRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,login',
            'password' => ['required', 'confirmed', new PasswordRule()],
            'first_name' => Helpers::$filledString255,
            'last_name' => Helpers::$filledString255,
            'patronymic' => Helpers::$filledString255,
            'roles' => 'filled|array',
            'roles.*.id' => 'required|integer|exists:roles,id'
        ];
    }
}

