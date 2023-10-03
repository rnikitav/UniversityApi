<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property integer $id
 * @property integer $file_id
 * @property string $file_type
 * @property string $category
 * @property string $disk
 * @property string $path
 * @property string $original_name
 * @property string $sha256
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

    public function file(): MorphTo
    {
        return $this->morphTo();
    }
}
