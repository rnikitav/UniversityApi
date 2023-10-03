<?php

namespace App\Models\Accelerator;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property integer $accelerator_id
 * @property string $name
 * @property Carbon $date_completion
 * @property integer $max_score
 *
 * @property Accelerator $accelerator
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
}