<?php

namespace App\Models\Accelerator\Case;

use App\Models\Accelerator\AcceleratorControlPoint;
use App\Models\User\User;
use App\Traits\HasFiles;
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
 * @property integer $control_point_id
 * @property integer $author_id
 * @property string $description
 * @property integer $status_id
 * @property integer $score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property AcceleratorCase $case
 * @property AcceleratorControlPoint $controlPoint
 * @property User $author
 * @property AcceleratorCaseSolutionStatus $status
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorCaseSolution extends Model
{
    use HasFactory, HasFiles;

    protected $table = 'accelerator_case_solutions';
    protected $fillable = [
        'case_id',
        'control_point_id',
        'author_id',
        'description',
        'status_id',
        'score',
    ];
    protected $with = ['author', 'status'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(AcceleratorCase::class);
    }

    public function controlPoint(): HasOne
    {
        return $this->hasOne(AcceleratorControlPoint::class, 'id', 'control_point_id');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    public function status(): HasOne
    {
        return $this->hasOne(AcceleratorCaseEventStatus::class, 'id', 'status_id');
    }
}
