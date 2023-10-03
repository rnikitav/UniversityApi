<?php

namespace Tests\Feature\Http\Controllers\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorControlPoint;
use App\Models\Accelerator\AcceleratorStatus;
use App\Models\User\User as UserModel;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use App\Utils\Resource as ResourceHelpers;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\Generators\Accelerator\Accelerator as AcceleratorGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group accelerators
 * @group accelerator
 */
class AcceleratorTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected AcceleratorModel $acceleratorTest;
    protected AcceleratorRepository $acceleratorRepository;
    protected array $itemStructure = [
        'id',
        'name',
        'description',
        'published_at',
        'date_end_accepting',
        'date_end',
        'status' => [
            'id',
            'name',
        ],
        'attachments' => [
            '*' => [
                'category',
                'path',
                'original_name',
            ]
        ],
        'control_points' => [
            '*' => [
                'id',
                'name',
                'date_completion',
                'max_score',
            ]
        ]
    ];
    protected array $itemListStructure = [
        'id',
        'name',
        'description',
        'status' => [
            'id',
            'name',
        ]
    ];
    protected array $minimalCreateData = [
         'name' => 'test',
         'date_end_accepting' => '2022-07-30',
         'date_end' => '2022-12-15',
         'control_points' => [
             [
                 'name' => 'test point',
                 'date_completion' => '2022-12-10',
                 'max_score' => 10,
             ]
         ],
     ];
    protected array $postHeaders = [
        'Content-Type' => 'multipart/form-data',
        'Accept' => 'application/json'
    ];

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = UserModel::first();
        $this->acceleratorTest = AcceleratorGenerator::createWithPoint($this->userAdmin);

        $this->acceleratorRepository = app()->make(AcceleratorRepository::class);
    }

    protected function getRoute(int $id = null): string
    {
        return route('accelerators.index') . ($id ? '/' . $id : '');
    }

    public function testGetList()
    {
        $count = 5;
        AcceleratorGenerator::create($this->userAdmin, $count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count + 1, 'data');
    }

    public function testGetListPaginate()
    {
        $count = 5;
        AcceleratorGenerator::create($this->userAdmin, $count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute() . '?' . http_build_query(['lazy' => true]));
        $response->assertOk()
            ->assertJsonStructure(['data', 'cursor'])
            ->assertJsonCount($count + 1, 'data');
    }

    public function testGetListCheckStructure()
    {
        AcceleratorGenerator::create($this->userAdmin, 5);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonStructure(['data' => ['*' => $this->itemListStructure]]);
    }

    public function testCheckPermission()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->post($this->getRoute(), $this->minimalCreateData, $this->postHeaders);
        $response->assertForbidden();

        $user->givePermissionTo('accelerators.edit');

        $response = $this->post($this->getRoute(), $this->minimalCreateData, $this->postHeaders);
        $response->assertOk();
    }

    public function testGetItemCheckStructure()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute($this->acceleratorTest->id));
        $response->assertOk()
            ->assertJsonFragment(['id' => $this->acceleratorTest->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testCreate()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->post($this->getRoute(), $this->minimalCreateData, $this->postHeaders);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        /** @var AcceleratorModel $acceleratorModel */
        $acceleratorModel = $this->acceleratorRepository->byId($response->json('id'));
        $this->assertNotNull($acceleratorModel);
        $this->assertEquals(AcceleratorStatus::notPublished(), $acceleratorModel->status->id);
        $this->assertTrue($this->userAdmin->is($acceleratorModel->user));
    }

    public function testCreateAcceptApplications()
    {
        $this->actingAs($this->userAdmin);

        $data = array_merge($this->minimalCreateData, ['published_at' => now()->format('Y-m-d')]);

        $response = $this->post($this->getRoute(), $data, $this->postHeaders);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        /** @var AcceleratorModel $acceleratorModel */
        $acceleratorModel = $this->acceleratorRepository->byId($response->json('id'));
        $this->assertNotNull($acceleratorModel);
        $this->assertEquals(AcceleratorStatus::acceptApplications(), $acceleratorModel->status->id);
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->post($this->getRoute(), Arr::except($this->minimalCreateData, 'name'), $this->postHeaders);
        $response->assertUnprocessable();

        $response = $this->post($this->getRoute(), Arr::except($this->minimalCreateData, 'date_end_accepting'), $this->postHeaders);
        $response->assertUnprocessable();

        $response = $this->post($this->getRoute(), Arr::except($this->minimalCreateData, 'date_end'), $this->postHeaders);
        $response->assertUnprocessable();

        $response = $this->post($this->getRoute(), Arr::except($this->minimalCreateData, 'control_points'), $this->postHeaders);
        $response->assertUnprocessable();
    }

    public function testUpdateMinimal()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($this->acceleratorTest->id));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $this->actingAs($this->userAdmin);

        $controlPoint = $this->acceleratorTest->controlPoints->first();
        $date_completion = now()->format('Y-m-d');
        $expectedPoint = $this->getPreviewPoint($controlPoint);
        $expectedPoint['date_completion'] = $date_completion;

        $data = [
            'published_at' => '2022-11-30',
            'date_end_accepting' => '2022-11-30',
            'date_end' => '2022-12-31',
            'control_points' => [$expectedPoint],
        ];

        $response = $this->patchJson($this->getRoute($this->acceleratorTest->id), $data);
        $response->assertOk()
            ->assertJsonFragment(array_merge($data, ['id' => $this->acceleratorTest->id]));

        $this->acceleratorTest->refresh();
        $this->assertEquals(AcceleratorStatus::acceptApplications(), $this->acceleratorTest->status->id);
    }

    protected function getPreviewPoint(AcceleratorControlPoint $point): array
    {
        return [
            'id' => $point->id,
            'name' => $point->name,
            'date_completion' => ResourceHelpers::formatDate($point->date_completion),
            'max_score' => $point->max_score,
        ];
    }

    public function testUpdateNotOwner()
    {
        $user = UserGenerator::createVerified();
        $user->givePermissionTo('accelerators.edit');

        $this->actingAs($user);

        $response = $this->patchJson($this->getRoute($this->acceleratorTest->id));
        $response->assertForbidden();
    }

    /**
     * @dataProvider incorrectPreviewValues
     */
    public function testValidationIncorrectFields($values)
    {
        $this->validationIncorrectFields($values);
    }

    protected function validationIncorrectFields(array $values): void
    {
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($this->acceleratorTest->id), $values);

        $response->assertUnprocessable()->assertJsonStructure(['message']);
    }

    public function incorrectPreviewValues(): array
    {
        $date = '12/01/2022';
        return [
            'published_at format' => [['published_at' => $date]],
            'date_end_accepting format' => [['date_end_accepting' => $date]],
            'date_end format' => [['date_end' => $date]],

            'control_points not exists' => [['control_points' => [['id' => 0]]]],
            'control_points date format' => [['control_points' => [['date_completion' => $date]]]],
        ];
    }
}
