<?php

namespace App\Models\Accelerator\Case;

use App\Models\Accelerator\Accelerator;
use App\Traits\HasFiles;
use App\Traits\HasMessages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $status_id
 * @property integer $participation_id
 *
 * @property Accelerator $accelerator
 * @property AcceleratorCaseStatus $status
 * @property AcceleratorCaseParticipation $participation
 * @property Collection $participants
 * @property Collection $files
 * @property Collection $messages
 * @property Collection $events
 * @property Collection $solutions
 * @property Collection $scores
 * @property AcceleratorCaseParticipant $owner
 *
 * @method static $this first()
 * @method static $this create(array $attributes = [])
 *
 * @mixin Builder
 * @mixin QueryBuilder
 */
class AcceleratorCase extends Model
{
    use HasFactory, HasFiles, HasMessages;

    protected $table = 'accelerator_cases';
    protected $fillable = [
        'name',
        'description',
        'status_id',
        'participation_id',
    ];
    protected $with = ['status', 'participation', 'participants'];

    protected array $savingParticipants = [];

    public function accelerator(): BelongsTo
    {
        return $this->belongsTo(Accelerator::class);
    }

    public function status(): HasOne
    {
        return $this->hasOne(AcceleratorCaseStatus::class, 'id', 'status_id');
    }

    public function participation(): HasOne
    {
        return $this->hasOne(AcceleratorCaseParticipation::class, 'id', 'participation_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AcceleratorCaseParticipant::class, 'case_id', 'id');
    }

    public function owner(): HasOne
    {
        return $this->hasOne(AcceleratorCaseParticipant::class, 'case_id', 'id')
            ->where('role_id', AcceleratorCaseRole::owner());
    }

    public function events(): HasMany
    {
        return $this->hasMany(AcceleratorCaseEvent::class, 'case_id', 'id');
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(AcceleratorCaseSolution::class, 'case_id', 'id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AcceleratorCaseScore::class, 'case_id', 'id');
    }

    public function canEditable(): bool
    {
        return $this->status->id == AcceleratorCaseStatus::sentRevision();
    }

    public function setParticipants(array $participants): void
    {
        $this->savingParticipants = $participants;
    }

    public function getParticipants(): array
    {
        return $this->savingParticipants;
    }
}
