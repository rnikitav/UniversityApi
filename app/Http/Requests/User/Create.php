<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Auth\Registration;

/**
 * @property array $roles
 */
class Create extends Registration
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'roles' => 'filled|array',
            'roles.*.id' => 'required|exists:roles,id'
        ]);
    }
}

