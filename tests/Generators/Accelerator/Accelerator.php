<?php

namespace Tests\Generators\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorControlPoint as AcceleratorControlPointModel;
use App\Models\File as FileModel;
use App\Models\User\User;
use Database\Factories\Accelerator\AcceleratorFactory;
use Illuminate\Database\Eloquent\Collection;

class Accelerator
{
    protected static function getBaseFactory(User $user, int $count = null, array $data = []): AcceleratorFactory
    {
        /** @var AcceleratorFactory $factory */
        $factory = AcceleratorModel::factory($count);
        if ($data) {
            $factory = $factory->state($data);
        } else {
            $factory = $factory->mock(['user_id' => $user->id]);
        }
        return $factory;
    }

    public static function create(User $user, int $count = null, array $data = []): AcceleratorModel | Collection
    {
        return static::getBaseFactory($user, $count, $data)->create();
    }

    public static function createFull(User $user, int $count = null, array $data = []): AcceleratorModel | Collection
    {
        return static::getBaseFactory($user, $count, $data)
            ->has(AcceleratorControlPointModel::factory()->mock(), 'controlPoints')
            ->has(FileModel::factory()->mock(), 'files')
            ->create();
    }
}
