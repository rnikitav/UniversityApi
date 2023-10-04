<?php

namespace App\Models\Accelerator\Case;

use App\Models\User\User;
use App\Traits\HasMessages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property integer $id
 * @property integer $case_id
 * @property integer $user_id
 * @property integer $score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property AcceleratorCase $case
 * @property User $user
 * @property Collection $messages
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorCaseScore extends Model
{
    use HasFactory, HasMessages;

    protected $table = 'accelerator_case_scores';
    protected $fillable = [
        'case_id',
        'user_id',
        'score',
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
