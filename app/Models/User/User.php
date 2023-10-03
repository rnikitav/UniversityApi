<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property integer $id
 * @property string $login
 * @property string $confirm_token
 *
 * @property Collection $roles
 *
 * @method static $this create(array $attributes = [])
 * @method static $this first()
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'login',
        'password',
        "confirm_token",
    ];

    protected $hidden = [
        'password',
        'remember_token',
        "confirm_token",
    ];

    public function setPassword(string $password): void
    {
        $this->update(['password' => Hash::make($password)]);
    }

    public function clearConfirmToken(): void
    {
        $this->update(['confirm_token' => null]);
    }

    public function findForPassport(string $login): User
    {
        return $this->where('login', $login)->first();
    }
}
