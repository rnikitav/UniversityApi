<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFiles
{
    public ?array $attachments;

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'file');
    }
}
