<?php

namespace App\Models\Accelerator;

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
class AcceleratorStatus extends Model
{
    protected $table = 'accelerator_statuses';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public static function notPublished(): string
    {
        return 'not_published';
    }

    public static function acceptApplications(): string
    {
        return 'accept_applications';
    }

    public static function completed(): string
    {
        return 'completed';
    }

    public static function solvingCases(): string
    {
        return 'solving_cases';
    }
}
