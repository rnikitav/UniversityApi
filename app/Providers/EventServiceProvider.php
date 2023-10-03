<?php

namespace App\Providers;

use App\Events\FileDeleting;
use App\Listeners\DeleteFile;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        FileDeleting::class => [
            [DeleteFile::class, 'handle']
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
