<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property integer $id
 * @property integer $owner_id
 * @property string $owner_type
 * @property string $category
 * @property string $disk
 * @property string $path
 * @property string $original_name
 * @property string $sha256
 *
 * @property Model $owner
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    protected $fillable = [
        'category',
        'path',
        'original_name',
        'disk',
        'sha256'
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
