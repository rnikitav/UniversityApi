<?php

namespace App\Factories;

use App\Http\Requests\Auth\Registration as RegistrationRequest;
use App\Http\Requests\User\Create as UserCreateRequest;
use App\Models\User\User as UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// TODO перенести функционал в database/factories/User/UserFactory.php
class User
{
    static function fromRegistrationRequest(RegistrationRequest $request): UserModel
    {
        return UserModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'confirm_token' => Str::random(40),
        ]);
    }

    static function fromCreateRequest(UserCreateRequest $request): UserModel
    {
        return UserModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now()
        ]);
    }
}
