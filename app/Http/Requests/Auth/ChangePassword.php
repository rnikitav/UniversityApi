<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $key
 * @property string $password
 */
class ChangePassword extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::min(8)]
        ];
    }
}

