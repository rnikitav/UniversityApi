<?php

namespace App\Models\News;

use App\Traits\HasFiles;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string $description
 * @property Carbon $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $files
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 * @method $this deleteFiles()
 */
class News extends Model
{
    use HasFactory, HasFiles, HasSlug;

    protected $fillable = ['slug', 'title', 'body', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function needDeleteOldFiles(): bool
    {
        return true;
    }
}
