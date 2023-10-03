<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

abstract class AbstractSimpleTableSeeder extends Seeder
{
    protected array $statuses = [];
    protected string $className;

    public function __construct()
    {
        $this->className = $this->getClassName();
    }

    abstract protected function getClassName(): string;

    public function run(): void
    {
        $exists = $this->className::all();
        foreach ($this->statuses as $needle => $data) {
            if (!$exists->contains('id', $needle)) {
                $this->className::create([
                    'id' => $needle,
                    'name' => $data
                ]);
            }
        }
    }
}
