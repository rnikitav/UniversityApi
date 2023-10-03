<?php

namespace App\Models\Accelerator\Case;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property string $id
 * @property string $name
 * @property boolean $active
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorCaseEventStatus extends Model
{
    protected $table = 'accelerator_case_event_statuses';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public static function submitted(): string
    {
        return 'submitted';
    }

    public static function approved(): string
    {
        return 'approved';
    }

    public static function rejected(): string
    {
        return 'rejected';
    }
}
