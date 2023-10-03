<?php

namespace Tests\Feature\Http\Controllers\Accelerator;

use App\Mail\Accelerator\EventCreate as EventCreateMail;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\Accelerator\Case\AcceleratorCase as AcceleratorCaseModel;
use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;
use App\Models\Accelerator\Case\AcceleratorCaseEventType;
use App\Models\Accelerator\Case\AcceleratorCaseParticipant;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
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

    protected function getUpdateData(string $status = null, int $newOwnerId = null): array
    {
        $data = [
            'status' => $status ?? AcceleratorCaseEventStatus::approved(),
        ];
        if ($newOwnerId) {
            $data['new_owner'] = $newOwnerId;
        }
        return $data;
    }

    public function testGetList()
    {
        $count = 5;
        AcceleratorCaseEventGenerator::createEnter($this->caseTest, $count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count)
            ->assertJsonStructure(['*' => $this->itemStructure]);
    }

    public function testGetListCheckPermission()
    {
        AcceleratorCaseEventGenerator::createEnter($this->caseTest);
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();
    }

    public function testGetItemCheckStructure()
    {
        $event = AcceleratorCaseEventGenerator::createEnter($this->caseTest);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute(id: $event->id));
        $response->assertOk()
            ->assertJsonFragment(['id' => $event->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testGetItemCheckPermission()
    {
        $event = AcceleratorCaseEventGenerator::createEnter($this->caseTest);
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

    public function testUpdateCheckPermissionOwnerCase()
    {
        $event = AcceleratorCaseEventGenerator::createEnter($this->caseTest);
        $eventExit = AcceleratorCaseEventGenerator::createExit($this->caseTest);

        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->patchJson($this->getRoute(id: $event->id), $this->getUpdateData());
        $response->assertForbidden();

        $this->caseTest->owner->update(['user_id' => $user->id]);
        $response = $this->patchJson($this->getRoute(id: $eventExit->id), $this->getUpdateData());
        $response->assertForbidden();

        $response = $this->patchJson($this->getRoute(id: $event->id), $this->getUpdateData());
        $response->assertOk();

        $response = $this->patchJson($this->getRoute(id: $event->id), $this->getUpdateData());
        $response->assertForbidden();
    }

    public function testUpdateCheckPermissionOwnerAccelerator()
    {
        $user = UserGenerator::createVerified();
        $this->caseTest->owner()->update(['user_id' => $user->id]);

        $event = AcceleratorCaseEventGenerator::createEnter($this->caseTest);

        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute(id: $event->id), $this->getUpdateData());
        $response->assertOk();

        $response = $this->patchJson($this->getRoute(id: $event->id), $this->getUpdateData());
        $response->assertForbidden();
    }

    public function testUpdateCheckNewOwner()
    {
        $user = UserGenerator::createVerified();
        $this->caseTest->owner()->update(['user_id' => $user->id]);

        $eventExit = AcceleratorCaseEventGenerator::createExit($this->caseTest);

        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute(id: $eventExit->id), $this->getUpdateData());
        $response->assertUnprocessable();

        $response = $this->patchJson($this->getRoute(id: $eventExit->id), $this->getUpdateData(newOwnerId: $user->id));
        $response->assertOk();
    }

    public function testUpdateEnter()
    {
        $user = UserGenerator::createVerified();
        $event = AcceleratorCaseEventGenerator::createEnter($this->caseTest);
        $event->update(['participant_id' => $user->id]);

        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute(id: $event->id), $this->getUpdateData(AcceleratorCaseEventStatus::rejected()));
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        $this->assertEquals(AcceleratorCaseEventStatus::rejected(), $event->refresh()->status->id);
        $this->assertTrue(
            $this->caseTest->refresh()
                ->participants->doesntContain('user_id', $user->id)
        );

        $event->update(['status_id' => AcceleratorCaseEventStatus::submitted()]);

        $response = $this->patchJson($this->getRoute(id: $event->id), $this->getUpdateData());
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        $this->assertEquals(AcceleratorCaseEventStatus::approved(), $event->refresh()->status->id);
        $this->assertTrue($this->userAdmin->is($event->refresh()->moderator));
        $this->assertTrue(
            $this->caseTest->refresh()
                ->participants->contains('user_id', $user->id)
        );
    }

    public function testUpdateExit()
    {
        $user = UserGenerator::createVerified();
        $this->caseTest->participants()->create(['user_id' => $user->id, 'role_id' => AcceleratorCaseRole::participant()]);

        $eventExit = AcceleratorCaseEventGenerator::createExit($this->caseTest);
        $eventExit->update(['participant_id' => $user->id]);

        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute(id: $eventExit->id), $this->getUpdateData());
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        $this->assertEquals(AcceleratorCaseEventStatus::approved(), $eventExit->refresh()->status->id);
        $this->assertTrue($this->userAdmin->is($eventExit->refresh()->moderator));
        $this->assertTrue(
            $this->caseTest->refresh()
                ->participants->doesntContain('user_id', $user->id)
        );
    }

    public function testUpdateExitOwner()
    {
        $user = UserGenerator::createVerified();
        $this->caseTest->participants()->create(['user_id' => $user->id, 'role_id' => AcceleratorCaseRole::participant()]);

        $this->actingAs($this->userAdmin);

        $eventExitOwner = AcceleratorCaseEventGenerator::createExit($this->caseTest);

        $response = $this->patchJson($this->getRoute(id: $eventExitOwner->id), $this->getUpdateData(newOwnerId: $user->id));
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        $this->assertEquals(AcceleratorCaseEventStatus::approved(), $eventExitOwner->refresh()->status->id);
        $this->assertTrue($this->userAdmin->is($eventExitOwner->refresh()->moderator));

        /** @var AcceleratorCaseParticipant $participant */
        $this->caseTest->refresh();
        $participant = $this->caseTest->participants->firstWhere('user_id', $user->id);
        $this->assertEquals(AcceleratorCaseRole::owner(), $participant->role->id);
        $this->assertTrue($this->caseTest->participants->doesntContain('user_id', $this->userAdmin->id));
    }
}
