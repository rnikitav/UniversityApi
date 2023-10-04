<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\Generators\Accelerator\Accelerator as AcceleratorGenerator;
use Tests\Generators\Accelerator\AcceleratorCase as AcceleratorCaseGenerator;
use Tests\TestCase;

/**
 * @group user_data
 * @group user_favorites
 */
class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected AcceleratorModel $acceleratorTest;
    protected array $responseStructure = [
        'id',
        'name',
        'description',
        'accelerator',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = User::first();
        $this->acceleratorTest = AcceleratorGenerator::create($this->userAdmin);
    }

    protected function getRoute(): string
    {
        return route('user.favorites.index');
    }

    protected function getRequestData(int $id, string $type = 'case'): array
    {
        return [
            'type' => $type,
            'id' => $id,
        ];
    }

    public function testGetList()
    {
        $cases = AcceleratorCaseGenerator::create($this->acceleratorTest, 5);
        $this->userAdmin->favoriteCases()->sync($cases->pluck('id'));
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure(['data' => ['*' => $this->responseStructure]]);
    }

    public function testCreate()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), $this->getRequestData($case->id));
        $response->assertOk()
            ->assertExactJson(['status' => true]);

        $this->assertNotNull($this->userAdmin->favoriteCases->firstWhere('id', $case->id));
    }

    public function testCreateIncorrect()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $this->actingAs($this->userAdmin);

        $data = $this->getRequestData($case->id);

        $response = $this->postJson($this->getRoute(), Arr::except($data, 'type'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), Arr::except($data, 'id'));
        $response->assertUnprocessable();
    }

    public function testDelete()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $this->userAdmin->favoriteCases()->attach($case->id);
        $this->actingAs($this->userAdmin);

        $response = $this->deleteJson($this->getRoute(), $this->getRequestData($case->id));
        $response->assertOk()
            ->assertExactJson(['status' => true]);

        $this->assertNull($this->userAdmin->favoriteCases->firstWhere('id', $case->id));
    }
}
