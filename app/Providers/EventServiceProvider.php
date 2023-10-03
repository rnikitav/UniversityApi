<?php

namespace App\Providers;

use App\Events\FileDeleting;
use App\Listeners\DeleteFile;
use App\Models\Accelerator\Accelerator;
use App\Observers\AcceleratorObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        FileDeleting::class => [
            [DeleteFile::class, 'handle']
        ],
    ];

    protected $observers = [
        Accelerator::class => [AcceleratorObserver::class],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
