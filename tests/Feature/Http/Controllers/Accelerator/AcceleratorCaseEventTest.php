<?php

namespace Tests\Feature\Http\Controllers\Accelerator;

use App\Mail\Accelerator\EventCreate as EventCreateMail;
use App\Mail\Accelerator\CaseUpdateStatus as CaseUpdateStatusMail;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;
use App\Models\Accelerator\Case\AcceleratorCaseEventType;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
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
use Tests\Generators\Accelerator\AcceleratorCaseEvent as AcceleratorCaseEventGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group accelerators
 * @group accelerator_case_event
 */
class AcceleratorCaseEventTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected AcceleratorModel $acceleratorTest;
    protected AcceleratorCaseModel $caseTest;
    protected AcceleratorRepository $acceleratorRepository;
    protected array $itemStructure = [
        'id',
        'description',
        'initializer' => [
            'id',
            'main_data'
        ],
        'type' => [
            'id',
            'name',
        ],
        'participant' => [
            'id',
            'main_data'
        ],
        'status' => [
            'id',
            'name',
        ],
        'moderator',
        'created_at',
        'updated_at'
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
        $this->caseTest = AcceleratorCaseGenerator::create($this->acceleratorTest);

        $this->acceleratorRepository = app()->make(AcceleratorRepository::class);
    }

    protected function getRoute(int $acceleratorId = null, int $caseId = null, int $id = null): string
    {
        return route('events.index', [
            'id' => $acceleratorId ?? $this->acceleratorTest->id,
            'case_id' => $caseId ?? $this->caseTest->id,
            ]) . ($id ? '/' . $id : '');
    }

    /*
    protected function getRouteChangeStatus(int $acceleratorId = null, int $id = null): string
    {
        return $this->getRoute($acceleratorId, $id) . '/change-status';
    }
    */

    protected function getMinimalCreateData(string $type = null, int $participantId = null): array
    {
        $data = [
            'description' => 'test description',
            'type' => $type ?? AcceleratorCaseEventType::enter(),
        ];
        if ($participantId) {
            $data['participant'] = $participantId;
        }
        return $data;
    }

    public function testGetList()
    {
        $count = 5;
        AcceleratorCaseEventGenerator::create($this->caseTest, $count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count)
            ->assertJsonStructure(['*' => $this->itemStructure]);
    }

    public function testGetListCheckPermission()
    {
        AcceleratorCaseEventGenerator::create($this->caseTest);
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();
    }

    public function testGetItemCheckStructure()
    {
        $event = AcceleratorCaseEventGenerator::create($this->caseTest);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute(id: $event->id));
        $response->assertOk()
            ->assertJsonFragment(['id' => $event->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testGetItemCheckPermission()
    {
        $event = AcceleratorCaseEventGenerator::create($this->caseTest);
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute(id: $event->id));
        $response->assertForbidden();
    }

    public function testCreateCheckPermissionStudent()
    {
        Mail::fake();

        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData());
        $response->assertForbidden();

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData(AcceleratorCaseEventType::exit()));
        $response->assertForbidden();

        $user->givePermissionTo(Permission::getPermissionStudent());

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData());
        $response->assertOk();

        $this->caseTest->participants()->create(['user_id' => $user->id, 'role_id' => AcceleratorCaseRole::participant()]);

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData(AcceleratorCaseEventType::exit()));
        $response->assertOk();

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData(AcceleratorCaseEventType::exit(), $this->userAdmin->id));
        $response->assertForbidden();

        Mail::assertSent(EventCreateMail::class);
    }

    public function testCreateCheckPermissionOwner()
    {
        Mail::fake();

        $user = UserGenerator::createVerified();
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData(participantId: $user->id));
        $response->assertOk();

        $this->caseTest->participants()->create(['user_id' => $user->id, 'role_id' => AcceleratorCaseRole::participant()]);

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData(AcceleratorCaseEventType::exit(), $user->id));
        $response->assertOk();

        Mail::assertSent(EventCreateMail::class);
    }

    public function testCreate()
    {
        Mail::fake();

        $user = UserGenerator::createVerified();
        $user->givePermissionTo(Permission::getPermissionStudent());
        $this->actingAs($user);

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData());
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        $event = $this->acceleratorRepository->eventByIdOr404($this->caseTest, $response->json('id'));
        $this->assertNotNull($event);
        $this->assertEquals(AcceleratorCaseEventStatus::submitted(), $event->status->id);
        $this->assertEquals(AcceleratorCaseEventType::enter(), $event->type->id);
        $this->assertTrue($user->is($event->initializer));
        $this->assertTrue($user->is($event->participant));

        Mail::assertSent(EventCreateMail::class);
    }

    public function testCreateIncorrect()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), Arr::except($this->getMinimalCreateData(), 'type'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), Arr::except($this->getMinimalCreateData(), 'description'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData(participantId: $this->userAdmin->id));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), $this->getMinimalCreateData(AcceleratorCaseEventType::exit(), $user->id));
        $response->assertUnprocessable();
    }

/*    public function testUpdateCheckStatus()
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
    */
}
