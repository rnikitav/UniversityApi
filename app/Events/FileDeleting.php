<?php

namespace App\Events;

use App\Models\File;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileDeleting
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public File $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }
}
