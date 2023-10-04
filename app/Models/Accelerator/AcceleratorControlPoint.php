<?php

namespace App\Models\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCaseSolution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property integer $id
 * @property integer $accelerator_id
 * @property string $name
 * @property Carbon $date_completion
 * @property integer $max_score
 *
 * @property Accelerator $accelerator
 * @property Collection $solutions
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorControlPoint extends Model
{
    use HasFactory;

    protected $table = 'accelerator_control_points';

    protected $fillable = [
        'accelerator_id',
        'name',
        'date_completion',
        'max_score',
    ];

    protected $casts = [
        'date_completion' => 'date',
    ];

    public function accelerator(): BelongsTo
    {
        return $this->belongsTo(Accelerator::class);
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(AcceleratorCaseSolution::class, 'control_point_id', 'id');
    }
}
