<?php

namespace App\Models\Accelerator\Case;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property integer $case_id
 * @property integer $user_id
 * @property string $message
 * @property Carbon $created_at
 *
 * @property AcceleratorCase $case
 * @property User $user
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

    public function case(): BelongsTo
    {
        return $this->belongsTo(AcceleratorCase::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
