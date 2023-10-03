<?php

namespace App\Models\Accelerator;

use App\Events\FileDeleting;
use App\Models\File;
use App\Models\User\User;
use App\Services\SaveFiles;
use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property Carbon $published_at
 * @property Carbon $date_end_accepting
 * @property Carbon $date_end
 * @property integer $user_id
 * @property string $status_id
 *
 * @property Collection $files
 * @property Collection $controlPoints
 * @property User $user
 * @property AcceleratorStatus $status
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class Accelerator extends Model
{
    use HasFactory, HasFiles;

    protected $table = 'accelerators';

    protected $fillable = [
        'name',
        'description',
        'published_at',
        'date_end_accepting',
        'date_end',

        // Вспомогательные поля. Не относятся к самой модели
        'control_points',
        'attachments'
    ];

    protected $casts = [
        'published_at' => 'date',
        'date_end_accepting' => 'date',
        'date_end' => 'date',
    ];


    public ?array $savingControlPoints;

    public static function boot()
    {
        parent::boot();

        static::saving(function(self $instance) {
            $instance->status_id = AcceleratorStatus::notPublished();
            $instance->savingControlPoints = Arr::pull($instance->attributes, 'control_points');
            $instance->attachments = Arr::pull($instance->attributes, 'files');
        });
        static::saved(function(self $instance) {
            SaveFiles::save($instance);
        });
        static::deleting(function (self $instance) {
            foreach ($instance->files as $file) {
                FileDeleting::dispatch($file);
                $file->delete();
            }
        });
    }

    public function controlPoints(): HasMany
    {
        return $this->hasMany(AcceleratorControlPoint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): HasOne
    {
        return $this->hasOne(AcceleratorStatus::class, 'id', 'status_id');
    }
}
