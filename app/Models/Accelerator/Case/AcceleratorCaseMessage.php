<?php

namespace App\Models\Accelerator\Case;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property integer $owner_id
 * @property string $owner_type
 * @property integer $user_id
 * @property string $message
 * @property Carbon $created_at
 *
 * @property User $user
 * @property Model $owner
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorCaseMessage extends Model
{
    protected $table = 'accelerator_case_messages';
    protected $fillable = [
        'user_id',
        'message',
    ];
    protected $with = ['user'];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
