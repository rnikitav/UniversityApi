<?php

namespace App\Models\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCase;
use App\Models\Accelerator\Case\AcceleratorCaseStatus;
use App\Models\File;
use App\Models\Tags\Tag;
use App\Models\User\User;
use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property Carbon $published_at
 * @property Carbon $date_end_accepting
 * @property Carbon $date_end
 * @property integer $user_id
 * @property integer $image_main_id
 * @property string $status_id
 * @property Carbon $created_at
 *
 * @property Collection $files
 * @property Collection $controlPoints
 * @property Collection $cases
 * @property Collection $approvedCases
 * @property Collection $tags
 * @property User $user
 * @property AcceleratorStatus $status
 * @property File $imageMain
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
        'user_id',
        'image_main_id',
    ];

    protected $casts = [
        'published_at' => 'date',
        'date_end_accepting' => 'date',
        'date_end' => 'date',
    ];

    protected $with = ['status'];

    protected array $savingControlPoints = [];
    protected array $savingTags = [];

    public function controlPoints(): HasMany
    {
        return $this->hasMany(AcceleratorControlPoint::class);
    }

    public function lastPoint(): ?AcceleratorControlPoint
    {
        return $this->controlPoints->sortByDesc('date_completion')->first();
    }

    public function cases(): HasMany
    {
        return $this->hasMany(AcceleratorCase::class);
    }

    public function approvedCases(): HasMany
    {
        return $this->hasMany(AcceleratorCase::class)
            ->where('status_id', AcceleratorCaseStatus::approved());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): HasOne
    {
        return $this->hasOne(AcceleratorStatus::class, 'id', 'status_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'accelerator_tags');
    }

    public function imageMain(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'image_main_id');
    }

    public function setControlPoints(array $points): void
    {
        $this->savingControlPoints = $points;
    }

    public function getControlPoints(): array
    {
        return $this->savingControlPoints;
    }

    public function setTags(array $tags): void
    {
        $this->savingTags = $tags;
    }

    public function getTags(): array
    {
        return $this->savingTags;
    }
}
