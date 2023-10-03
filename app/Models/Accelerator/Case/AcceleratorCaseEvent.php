<?php

namespace App\Models\Accelerator\Case;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property integer $case_id
 * @property integer $initializer_id
 * @property integer $type_id
 * @property string $description
 * @property integer $participant_id
 * @property integer $status_id
 * @property integer $moderator_id
 *
 * @property AcceleratorCase $case
 * @property User $initializer
 * @property AcceleratorCaseEventType $type
 * @property User $participant
 * @property AcceleratorCaseEventStatus $status
 * @property User $moderator
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorCaseEvent extends Model
{
    use HasFactory;

    protected $table = 'accelerator_case_events';
    protected $fillable = [
        'initializer_id',
        'type_id',
        'description',
        'participant_id',
        'status_id',
        'moderator_id',
    ];
    protected $with = ['initializer', 'type', 'participant', 'status', 'moderator'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(AcceleratorCase::class);
    }

    public function initializer(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'initializer_id');
    }

    public function type(): HasOne
    {
        return $this->hasOne(AcceleratorCaseEventType::class, 'id', 'type_id');
    }

    public function participant(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'participant_id');
    }

    public function status(): HasOne
    {
        return $this->hasOne(AcceleratorCaseEventStatus::class, 'id', 'status_id');
    }

    public function moderator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'moderator_id');
    }
}
