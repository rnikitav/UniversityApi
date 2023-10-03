<?php

namespace App\Providers;

use App\Events\FileDeleting;
use App\Listeners\DeleteFile;
use App\Models\Accelerator\Accelerator;
use App\Models\Accelerator\Case\AcceleratorCase;
use App\Observers\AcceleratorCaseObserver;
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
        AcceleratorCase::class => [AcceleratorCaseObserver::class],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
