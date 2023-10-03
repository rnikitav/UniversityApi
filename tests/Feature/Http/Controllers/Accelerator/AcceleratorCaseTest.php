<?php

namespace Tests\Feature\Http\Controllers\Accelerator;

use App\Mail\Accelerator\CaseCreate as CaseCreateMail;
use App\Mail\Accelerator\CaseUpdateStatus as CaseUpdateStatusMail;
use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCaseStatus;
use App\Models\Permissions\Permission;
use App\Models\User\User as UserModel;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Tests\Generators\Accelerator\Accelerator as AcceleratorGenerator;
use Tests\Generators\Accelerator\AcceleratorCase as AcceleratorCaseGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group accelerators
 * @group accelerator_case
 */
class AcceleratorCaseTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected AcceleratorModel $acceleratorTest;
    protected AcceleratorRepository $acceleratorRepository;
    protected array $itemStructure = [
        'id',
        'name',
        'description',
        'status' => [
            'id',
            'name',
        ],
        'participation' => [
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
        'participants' => [
            '*' => [
                'id',
                'user' => [
                    'id',
                    'main_data',
                ],
                'role' => [
                    'id',
                    'name',
                ]
            ]
        ],
        'messages' => [
            '*' => [
                'id',
                'user' => [
                    'id',
                    'main_data',
                ],
                'message',
                'at',
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
        ],
        'participation' => [
            'id',
            'name',
        ],
        'owner' => [
            'id',
            'main_data' => [
                'first_name',
                'last_name',
                'patronymic',
                'email',
            ]
        ]
    ];
    protected array $minimalCreateData = [
        'name' => 'test',
        'description' => 'test description',
        'participation' => 'single',
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
        $this->acceleratorTest = AcceleratorGenerator::create($this->userAdmin);

        $this->acceleratorRepository = app()->make(AcceleratorRepository::class);
    }

    protected function getRoute(int $acceleratorId = null, int $id = null): string
    {
        return route('cases.index', ['id' => $acceleratorId ?? $this->acceleratorTest->id]) . ($id ? '/' . $id : '');
    }

    protected function getRouteChangeStatus(int $acceleratorId = null, int $id = null): string
    {
        return $this->getRoute($acceleratorId, $id) . '/change-status';
    }

    public function testGetListOwner()
    {
        $count = 5;
        AcceleratorCaseGenerator::create($this->acceleratorTest, $count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count)
            ->assertJsonStructure(['*' => $this->itemListStructure]);
    }

    public function testGetList()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $user = UserGenerator::createVerified();

        $this->actingAs($user);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount(0);

        $case->status_id = AcceleratorCaseStatus::approved();
        $case->save();

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount(1);
    }

    public function testGetItemCheckStructure()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute(id: $case->id));
        $response->assertOk()
            ->assertJsonFragment(['id' => $case->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testGetItemCheckPermission()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute(id: $case->id));
        $response->assertForbidden();
    }

    public function testCheckCreatePermission()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->post($this->getRoute(), $this->minimalCreateData, $this->postHeaders);
        $response->assertForbidden();

        $user->givePermissionTo(Permission::getPermissionStudent());

        $response = $this->post($this->getRoute(), $this->minimalCreateData, $this->postHeaders);
        $response->assertOk();
    }

    public function testCreate()
    {
        Mail::fake();
        $this->actingAs($this->userAdmin);

        $response = $this->post($this->getRoute(), $this->minimalCreateData, $this->postHeaders);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        $acceleratorCaseModel = $this->acceleratorRepository->caseByIdOr404($this->acceleratorTest, $response->json('id'));
        $this->assertNotNull($acceleratorCaseModel);
        $this->assertEquals(AcceleratorCaseStatus::submitted(), $acceleratorCaseModel->status->id);
        $this->assertTrue($this->userAdmin->is($acceleratorCaseModel->owner?->user));

        Mail::assertSent(function(CaseCreateMail $mail) use ($acceleratorCaseModel) {
            return $mail->case->is($acceleratorCaseModel);
        });
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->post($this->getRoute(), Arr::except($this->minimalCreateData, 'name'), $this->postHeaders);
        $response->assertUnprocessable();

        $response = $this->post($this->getRoute(), Arr::except($this->minimalCreateData, 'description'), $this->postHeaders);
        $response->assertUnprocessable();

        $response = $this->post($this->getRoute(), Arr::except($this->minimalCreateData, 'participation'), $this->postHeaders);
        $response->assertUnprocessable();
    }

    public function testUpdateCheckStatus()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);

        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute(id: $case->id));
        $response->assertForbidden();

        $case->status_id = AcceleratorCaseStatus::sentRevision();
        $case->save();

        $response = $this->patchJson($this->getRoute(id: $case->id));
        $response->assertOk();
    }

    public function testUpdateCheckOwner()
    {
        $case = AcceleratorCaseGenerator::createWithStatus($this->acceleratorTest, AcceleratorCaseStatus::sentRevision());
        $user = UserGenerator::createVerified();

        $this->actingAs($user);

        $response = $this->patchJson($this->getRoute(id: $case->id));
        $response->assertForbidden();

        $case->owner->user_id = $user->id;
        $case->owner->save();

        $response = $this->patchJson($this->getRoute(id: $case->id));
        $response->assertOk();
    }

    public function testUpdateMinimal()
    {
        $case = AcceleratorCaseGenerator::createWithStatus($this->acceleratorTest, AcceleratorCaseStatus::sentRevision());
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute(id: $case->id));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $case = AcceleratorCaseGenerator::createWithStatus($this->acceleratorTest, AcceleratorCaseStatus::sentRevision());

        $this->actingAs($this->userAdmin);

        $expect = array_merge(Arr::except($this->minimalCreateData, 'participation'), ['id' => $case->id]);

        $response = $this->patchJson($this->getRoute(id: $case->id), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonFragment($expect);

        $this->assertEquals($response->json('participation.id'), $this->minimalCreateData['participation']);
    }

    public function testUpdateStatusCheckOwner()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $user = UserGenerator::createVerified();

        $case->owner->user_id = $user->id;
        $case->owner->save();

        $this->actingAs($user);

        $dataSubmitted = [
            'status' => AcceleratorCaseStatus::submitted()
        ];
        $dataApproved = [
            'status' => AcceleratorCaseStatus::approved()
        ];

        $response = $this->patchJson($this->getRouteChangeStatus(id: $case->id), $dataSubmitted);
        $response->assertForbidden();

        $case->status_id = AcceleratorCaseStatus::sentRevision();
        $case->save();

        $response = $this->patchJson($this->getRouteChangeStatus(id: $case->id), $dataApproved);
        $response->assertForbidden();

        $response = $this->patchJson($this->getRouteChangeStatus(id: $case->id), $dataSubmitted);
        $response->assertOk();
    }

    public function testUpdateStatusCheckModerator()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $user = UserGenerator::createVerified();

        $case->owner->user_id = $user->id;
        $case->owner->save();

        $this->actingAs($this->userAdmin);

        $dataSubmitted = [
            'status' => AcceleratorCaseStatus::submitted()
        ];
        $dataApproved = [
            'status' => AcceleratorCaseStatus::approved()
        ];

        $response = $this->patchJson($this->getRouteChangeStatus(id: $case->id), $dataSubmitted);
        $response->assertForbidden();

        $response = $this->patchJson($this->getRouteChangeStatus(id: $case->id), $dataApproved);
        $response->assertOk();
    }

    public function testUpdateStatus()
    {
        Mail::fake();
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);
        $user = UserGenerator::createVerified();

        $case->owner->user_id = $user->id;
        $case->owner->save();

        $this->actingAs($this->userAdmin);

        $newStatus = AcceleratorCaseStatus::approved();
        $message = 'Case is approved';
        $data = [
            'status' => $newStatus,
            'message' => $message,
        ];

        $response = $this->patchJson($this->getRouteChangeStatus(id: $case->id), $data);
        $response->assertOk();

        $case->refresh();
        $this->assertEquals($newStatus, $case->status_id);
        $this->assertEquals(1, $case->messages->count());
        $this->assertEquals($message, $case->messages->first()?->message);

        Mail::assertSent(function(CaseUpdateStatusMail $mail) use ($case) {
            return $mail->case->is($case);
        });
    }
}
