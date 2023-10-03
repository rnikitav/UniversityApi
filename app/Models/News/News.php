<?php

namespace App\Models\News;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

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
}
