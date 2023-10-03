<?php

namespace App\Models\Accelerator;

use App\Events\FileDeleting;
use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
