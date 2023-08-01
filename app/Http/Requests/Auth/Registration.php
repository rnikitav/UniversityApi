<?php

namespace App\Http\Requests\Auth;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $email
 * @property string $name
 * @property string $password
 */
class Registration extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            // TODO поле удалено из таблицы users
//            'name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', new PasswordRule()]
        ];
    }
}

