<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

/**
 * @property string $email
 * @property string $password
 * @property array $roles
 */
abstract class AbstractUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function prepareDataForCreateUser(): array
    {
        $mainDataKeys = ['email', 'first_name', 'last_name', 'patronymic'];

        $data = [
            'main_data' => array_reduce($mainDataKeys, function ($initial, $key) {
                if ($this->has($key)) {
                    $initial[$key] = $this->{$key};
                }
                return $initial;
            }, []),
            'sync_roles' => $this->roles ? Arr::pluck($this->roles, 'id') : null,
        ];

        if ($this->email) {
            $data['login'] = $this->email;
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        return $data;
    }
}

