<?php

namespace App\Models\Tags;

use App\Models\Permissions\Permission;
use App\Models\User\User;
use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property string $name
 * @property Carbon $created_at
 *
 * @property Collection $files
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class ImageCollection extends Model
{
    use HasFactory, HasFiles;

    protected $table = 'image_collections';

    protected $fillable = [
        'name',
    ];

    protected $with = ['files'];

    public function canDeleteFiles(): bool
    {
        /** @var User $currentUser */
        $currentUser = request()->user();
        return $currentUser->hasAnyPermission(['image-collection.edit', Permission::getPermissionAdministrator()]);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_image_collections');
    }
}
