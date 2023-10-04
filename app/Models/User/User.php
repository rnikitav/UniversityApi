<?php

namespace App\Models\User;

use App\Models\Accelerator\Accelerator;
use App\Models\Accelerator\Case\AcceleratorCase;
use App\Services\User\MainData as MainDataService;
use App\Services\User\SyncRoles as SyncRolesService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property integer $id
 * @property string $login
 * @property string $confirm_token
 * @property boolean $external
 *
 * @property Collection $roles
 * @property Collection $accelerators
 * @property Collection $favoriteCases
 * @property UserMainData $mainData
 * @property array $mainDataForUpdate
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
        'confirm_token',
        'external',

        // Вспомогательные поля. Не относятся к самой модели
        'main_data',
        'sync_roles'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'confirm_token',
    ];

    protected $casts = [
        'external' => 'boolean'
    ];

    public ?array $mainDataForUpdate;
    public ?array $rolesForUpdate;

    protected static function boot()
    {
        parent::boot();

        static::saving(function(self $instance) {
            $instance->mainDataForUpdate = Arr::pull($instance->attributes, 'main_data');
            $instance->rolesForUpdate = Arr::pull($instance->attributes, 'sync_roles');
        });
        static::saved(function(self $instance) {
            MainDataService::update($instance);
            SyncRolesService::update($instance);
        });
    }

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

    public function mainData(): HasOne
    {
        return $this->hasOne(UserMainData::class, 'user_id', 'id');
    }

    public function accelerators(): HasMany
    {
        return $this->hasMany(Accelerator::class, 'user_id', 'id');
    }

    public function favoriteCases(): MorphToMany
    {
        return $this->morphedByMany(AcceleratorCase::class, 'subject', 'user_favorites');
    }
}
