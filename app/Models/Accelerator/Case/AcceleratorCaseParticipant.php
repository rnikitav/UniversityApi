<?php

namespace App\Models\Accelerator\Case;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property integer $id
 * @property integer $case_id
 * @property integer $user_id
 * @property integer $role_id
 *
 * @property AcceleratorCase $case
 * @property User $user
 * @property AcceleratorCaseRole $role
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorCaseParticipant extends Model
{
    use HasFactory;

    protected $table = 'accelerator_case_participants';
    protected $fillable = [
        'user_id',
        'role_id',
    ];
    protected $with = ['role', 'user'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(AcceleratorCase::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function role(): HasOne
    {
        return $this->hasOne(AcceleratorCaseRole::class, 'id', 'role_id');
    }
}
