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
class AcceleratorCaseParticipation extends Model
{
    protected $table = 'accelerator_case_participations';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public static function single(): string
    {
        return 'single';
    }

    public static function group(): string
    {
        return 'group';
    }
}
