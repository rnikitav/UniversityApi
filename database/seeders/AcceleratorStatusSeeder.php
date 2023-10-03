<?php

namespace Database\Seeders;

use App\Models\Accelerator\AcceleratorStatus;
use Illuminate\Database\Seeder;

class AcceleratorStatusSeeder extends Seeder
{
    protected array $statuses;

    public function __construct()
    {
        $this->statuses = [
            AcceleratorStatus::notPublished() => 'Не опубликовано',
            AcceleratorStatus::acceptApplications() => 'Прием заявок',
            AcceleratorStatus::solvingCases() => 'Решение кейсов',
            AcceleratorStatus::completed() => 'Завершен',
        ];
    }

    public function run(): void
    {
        $exists = AcceleratorStatus::all();
        foreach ($this->statuses as $needle => $data) {
            if (!$exists->contains('id', $needle)) {
                AcceleratorStatus::create([
                    'id' => $needle,
                    'name' => $data
                ]);
            }
        }
    }
}
