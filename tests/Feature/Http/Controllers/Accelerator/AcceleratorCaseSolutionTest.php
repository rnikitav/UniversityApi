<?php

namespace Tests\Feature\Http\Controllers\Accelerator;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\AcceleratorControlPoint as AcceleratorControlPointModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
use App\Models\Accelerator\Case\AcceleratorCaseSolutionStatus;
use App\Models\User\User as UserModel;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\Generators\Accelerator\Accelerator as AcceleratorGenerator;
use Tests\Generators\Accelerator\AcceleratorCase as AcceleratorCaseGenerator;
use Tests\Generators\Accelerator\AcceleratorCaseSolution;
use Tests\Generators\Accelerator\AcceleratorCaseSolution as AcceleratorCaseSolutionGenerator;
use Tests\Generators\Accelerator\AcceleratorControlPoint as AcceleratorControlPointGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group accelerators
 * @group accelerator_case_solution
 */
class AcceleratorCaseSolutionTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected AcceleratorModel $acceleratorTest;
    protected AcceleratorCaseModel $caseTest;
    protected AcceleratorControlPointModel $pointTest;
    protected AcceleratorRepository $acceleratorRepository;
    protected array $itemStructure = [
        'id',
        'description',
        'control_point' => [
            'id',
            'name',
            'date_completion',
            'max_score',
        ],
        'author' => [
            'id',
            'main_data'
        ],
        'status' => [
            'id',
            'name',
        ],
        'created_at',
        'updated_at',
        'attachments' => [
            '*' => [
                'category',
                'path',
                'original_name',
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
        $this->acceleratorTest = AcceleratorGenerator::createWithControlPoint($this->userAdmin);
        $this->caseTest = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $this->pointTest = $this->acceleratorTest->controlPoints->first();
        $this->pointTest->update(['date_completion' => now()->addDay()]);

        $this->acceleratorRepository = app()->make(AcceleratorRepository::class);
    }

    protected function getRoute(int $acceleratorId = null, int $caseId = null, int $id = null): string
    {
        return route('solutions.index', [
            'id' => $acceleratorId ?? $this->acceleratorTest->id,
            'case_id' => $caseId ?? $this->caseTest->id,
            ]) . ($id ? '/' . $id : '');
    }

    protected function getCreateData(int $pointId = null): array
    {
        return [
            'control_point' => $pointId ?? $this->pointTest->id,
            'description' => 'test description',
        ];
    }

    protected function getUpdateData(string $status = null, int $score = null): array
    {
        return [
            'status' => $status ?? AcceleratorCaseSolutionStatus::approved(),
            'score' => $score ?? 100,
        ];
    }

    public function testGetList()
    {
        $count = 5;
        AcceleratorCaseSolutionGenerator::create($this->caseTest, $this->pointTest, $count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count)
            ->assertJsonStructure(['*' => $this->itemStructure]);
    }

    public function testGetItemCheckStructure()
    {
        $solution = AcceleratorCaseSolutionGenerator::create($this->caseTest, $this->pointTest);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute(id: $solution->id));
        $response->assertOk()
            ->assertJsonFragment(['id' => $solution->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testCreateCheckParticipant()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->post($this->getRoute(), $this->getCreateData(), $this->postHeaders);
        $response->assertForbidden();

        $this->caseTest->participants()->create(['user_id' => $user->id, 'role_id' => AcceleratorCaseRole::participant()]);

        $response = $this->post($this->getRoute(), $this->getCreateData(), $this->postHeaders);
        $response->assertOk();
    }

    public function testCreateCheckDateCompletion()
    {
        $this->actingAs($this->userAdmin);

        $this->pointTest->update(['date_completion' => now()->addDays(-1)->startOfDay()]);

        $response = $this->post($this->getRoute(), $this->getCreateData(), $this->postHeaders);
        $response->assertForbidden();

        $this->pointTest->update(['date_completion' => now()->addDay()]);

        $response = $this->post($this->getRoute(), $this->getCreateData(), $this->postHeaders);
        $response->assertOk();
    }

    public function testCreateCheckPrevPoint()
    {
        $this->actingAs($this->userAdmin);
        $newPoint = AcceleratorControlPointGenerator::create($this->acceleratorTest);
        $newPoint->update(['date_completion' => now()->addDays(10)]);

        $response = $this->post($this->getRoute(), $this->getCreateData($newPoint->id), $this->postHeaders);
        $response->assertForbidden();

        $solution = AcceleratorCaseSolutionGenerator::create($this->caseTest, $this->pointTest);
        $solution->update(['status_id' => AcceleratorCaseSolutionStatus::approved()]);

        $response = $this->post($this->getRoute(), $this->getCreateData($newPoint->id), $this->postHeaders);
        $response->assertOk();
    }

    public function testCreate()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->post($this->getRoute(), $this->getCreateData(), $this->postHeaders);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        $solution = $this->acceleratorRepository->solutionByIdOr404($this->caseTest, $response->json('id'));
        $this->assertNotNull($solution);
        $this->assertEquals(AcceleratorCaseSolutionStatus::submitted(), $solution->status->id);
        $this->assertTrue($this->pointTest->is($solution->controlPoint));
        $this->assertTrue($this->userAdmin->is($solution->author));
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->post($this->getRoute(), Arr::except($this->getCreateData(), 'control_point'), $this->postHeaders);
        $response->assertUnprocessable();

        $response = $this->post($this->getRoute(), Arr::except($this->getCreateData(), 'description'), $this->postHeaders);
        $response->assertUnprocessable();
    }

    public function testUpdateCheckPermission()
    {
        $solution = AcceleratorCaseSolutionGenerator::create($this->caseTest, $this->pointTest);
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $maxScore = $this->pointTest->max_score;

        $response = $this->patchJson($this->getRoute(id: $solution->id), $this->getUpdateData(score: $maxScore));
        $response->assertForbidden();

        $this->acceleratorTest->update(['user_id' => $user->id]);

        $response = $this->patchJson($this->getRoute(id: $solution->id), $this->getUpdateData(score: $maxScore));
        $response->assertOk();
    }

    public function testUpdateCheckScore()
    {
        $solution = AcceleratorCaseSolutionGenerator::create($this->caseTest, $this->pointTest);
        $this->actingAs($this->userAdmin);

        $maxScore = $this->pointTest->max_score;

        $response = $this->patchJson($this->getRoute(id: $solution->id), $this->getUpdateData(score: $maxScore + 1));
        $response->assertUnprocessable();

        $response = $this->patchJson($this->getRoute(id: $solution->id), $this->getUpdateData(score: -1));
        $response->assertUnprocessable();
    }

    public function testUpdateIncorrect()
    {
        $solution = AcceleratorCaseSolutionGenerator::create($this->caseTest, $this->pointTest);
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute(id: $solution->id), Arr::except($this->getUpdateData(), 'status'));
        $response->assertUnprocessable();

        $response = $this->patchJson($this->getRoute(id: $solution->id), Arr::except($this->getUpdateData(), 'score'));
        $response->assertUnprocessable();
    }

    public function testUpdate()
    {
        $solution = AcceleratorCaseSolutionGenerator::create($this->caseTest, $this->pointTest);
        $this->actingAs($this->userAdmin);

        $maxScore = $this->pointTest->max_score;

        $data = $this->getUpdateData(score: $maxScore);
        $data['message'] = 'test message';

        $response = $this->patchJson($this->getRoute(id: $solution->id), $data);
        $response->assertOk()
            ->assertJsonFragment(['id' => $solution->id])
            ->assertJsonStructure($this->itemStructure);

        $solution->refresh();

        $this->assertEquals(AcceleratorCaseSolutionStatus::approved(), $solution->status->id);
        $this->assertEquals($maxScore, $solution->score);
        $this->assertEquals(1, $this->caseTest->messages->count());
    }
}
