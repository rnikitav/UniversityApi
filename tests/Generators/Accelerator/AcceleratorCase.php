<?php

namespace Tests\Generators\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorControlPoint as AcceleratorControlPointModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseParticipant as AcceleratorCaseParticipantModel;
use App\Models\Accelerator\Case\AcceleratorCaseEvent as AcceleratorCaseEventModel;
use App\Models\Accelerator\Case\AcceleratorCaseSolution as AcceleratorCaseSolutionModel;
use App\Models\Accelerator\Case\AcceleratorCaseScore as AcceleratorCaseScoreModel;
use App\Models\File as FileModel;
use Database\Factories\Accelerator\Case\AcceleratorCaseFactory;
use Illuminate\Database\Eloquent\Collection;

class AcceleratorCase
{
    protected static function getBaseFactory(int $count = null, array $data = []): AcceleratorCaseFactory
    {
        /** @var AcceleratorCaseFactory $factory */
        $factory = AcceleratorCaseModel::factory($count);
        if ($data) {
            $factory = $factory->state($data);
        } else {
            $factory = $factory->mock();
        }
        return $factory;
    }

    public static function create(AcceleratorModel $accelerator, int $count = null, array $data = []): AcceleratorCaseModel | Collection
    {
        return static::getBaseFactory($count, $data)->accelerator($accelerator->id)
            ->has(AcceleratorCaseParticipantModel::factory()->mock($accelerator->user->id), 'participants')
            ->create();
    }

    public static function createWithStatus(AcceleratorModel $accelerator, string $statusId): AcceleratorCaseModel
    {
        return static::getBaseFactory()->accelerator($accelerator->id)
            ->has(AcceleratorCaseParticipantModel::factory()->mock($accelerator->user->id), 'participants')
            ->status($statusId)
            ->create();
    }

    public static function createWithFile(AcceleratorModel $accelerator): AcceleratorCaseModel | Collection
    {
        return static::getBaseFactory()->accelerator($accelerator->id)
            ->has(AcceleratorCaseParticipantModel::factory()->mock($accelerator->user->id), 'participants')
            ->has(FileModel::factory()->mock(), 'files')
            ->create();
    }

    public static function createWithEvent(AcceleratorModel $accelerator): AcceleratorCaseModel | Collection
    {
        return static::getBaseFactory()->accelerator($accelerator->id)
            ->has(AcceleratorCaseParticipantModel::factory()->mock($accelerator->user->id), 'participants')
            ->has(AcceleratorCaseEventModel::factory()->mock($accelerator->user->id), 'events')
            ->create();
    }

    public static function createWithSolution(AcceleratorModel $accelerator, AcceleratorControlPointModel $point): AcceleratorCaseModel | Collection
    {
        return static::getBaseFactory()
            ->accelerator($accelerator->id)
            ->has(AcceleratorCaseParticipantModel::factory()->mock($accelerator->user->id), 'participants')
            ->has(AcceleratorCaseSolutionModel::factory()->point($point->id)->mock($accelerator->user->id), 'solutions')
            ->create();
    }

    public static function createWithScore(AcceleratorModel $accelerator): AcceleratorCaseModel | Collection
    {
        return static::getBaseFactory()
            ->accelerator($accelerator->id)
            ->has(AcceleratorCaseParticipantModel::factory()->mock($accelerator->user->id), 'participants')
            ->has(AcceleratorCaseScoreModel::factory()->mock($accelerator->user->id), 'scores')
            ->create();
    }
}
