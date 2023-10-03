<?php

namespace App\Http\Controllers\Accelerator;

use App\Exceptions\OperationNotPermittedException;
use App\Http\Requests\Accelerator\Case\CreateEvent as CreateRequest;
use App\Http\Requests\Accelerator\Case\UpdateEvent as UpdateRequest;
use App\Http\Resources\Accelerator\Event as EventResource;
use App\Models\Accelerator\Case\AcceleratorCaseEvent;
use App\Models\Accelerator\Case\AcceleratorCaseEventType;
use App\Models\Accelerator\Case\AcceleratorCaseEventStatus;
use App\Models\Accelerator\Case\AcceleratorCaseParticipant;
use App\Models\Accelerator\Case\AcceleratorCaseRole;
use App\Models\Permissions\Permission;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class AcceleratorCaseEventController extends AbstractAcceleratorCaseController
{
    protected ?AcceleratorCaseEvent $event;
    protected ?AcceleratorCaseParticipant $processedParticipant;

    public function index(int $id, int $case_id): Response
    {
        $this->getAcceleratorCase($id, $case_id);
        $this->checkOwners();

        return response(EventResource::collection($this->case->events));
    }

    /**
     * @throws Throwable
     */
    public function store(CreateRequest $request, int $id, int $case_id): Response
    {
        $data = $request->prepareData();

        $this->getAcceleratorCase($id, $case_id);

        $this->checkPermissionStore($data['type_id'], $data['participant_id']);

        $hasParticipant = $this->case->participants->contains('user_id', $data['participant_id']);
        if ($data['type_id'] == AcceleratorCaseEventType::enter()) {
            if ($hasParticipant) {
                throw new UnprocessableEntityHttpException(__('exception.exists_participant'));
            }
        } else if ($data['type_id'] == AcceleratorCaseEventType::exit()) {
            if (!$hasParticipant) {
                throw new UnprocessableEntityHttpException(__('exception.undefined_participant'));
            }
        }

        $new = DBUtils::inTransaction(function () use ($data) {
            return $this->case->events()->create($data);
        });

        return response(new EventResource($new->refresh()));
    }

    public function show(int $id, int $case_id, int $event_id): Response
    {
        $this->getAcceleratorCase($id, $case_id);
        $this->checkOwners();

        $event = $this->acceleratorRepository->eventByIdOr404($this->case, $event_id);

        return response(new EventResource($event));
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, int $id, int $case_id, int $event_id): Response
    {
        $data = $request->prepareData();

        $this->getAcceleratorCase($id, $case_id);
        $this->checkOwners();

        $this->event = $this->acceleratorRepository->eventByIdOr404($this->case, $event_id);
        $this->checkPermissionUpdate();

        $this->getProcessedParticipant();
        if ($data['status_id'] == AcceleratorCaseEventStatus::approved()) {
            $this->checkNewOwner($data['new_owner'] ?? null);
        }

        DBUtils::inTransaction(function () use ($data) {
            $this->event->update($data);

            if ($data['status_id'] == AcceleratorCaseEventStatus::approved()) {
                $this->event->apply($data['new_owner'] ?? null);
            }
        });

        return response(new EventResource($this->event->refresh()));
    }

    protected function checkPermissionStore(string $typeEvent, int $participantId): void
    {
        $isStudent = $this->currentUser->hasPermissionTo(Permission::getPermissionStudent());
        $isOwner = $this->currentUser->is($this->accelerator->user);

        if ($isOwner) {
            return;
        }

        if ($typeEvent == AcceleratorCaseEventType::enter()) {
            if (!$isStudent) {
                throw new OperationNotPermittedException();
            }
        } else if ($typeEvent == AcceleratorCaseEventType::exit()) {
            $isParticipant = $this->case->participants->contains('user_id', $this->currentUser->id);

            if (!$isParticipant) {
                throw new OperationNotPermittedException();
            }
        }

        if ($this->currentUser->id != $participantId) {
            throw new OperationNotPermittedException();
        }
    }

    protected function checkPermissionUpdate(): void
    {
        $isOwner = $this->currentUser->is($this->accelerator->user);

        if ($this->event->type->id == AcceleratorCaseEventType::exit() && !$isOwner) {
            throw new OperationNotPermittedException();
        }

        if ($this->event->status->id != AcceleratorCaseEventStatus::submitted()) {
            throw new OperationNotPermittedException();
        }
    }

    protected function getProcessedParticipant(): void
    {
        $this->processedParticipant = $this->case->participants->first(
            fn (AcceleratorCaseParticipant $participant) => $participant->user->is($this->event->participant)
        );
    }

    protected function checkNewOwner(?int $newOwnerId): void
    {
        if ($this->event->type->id == AcceleratorCaseEventType::exit()
            && $this->processedParticipant->role->id == AcceleratorCaseRole::owner()
        ) {
            $newOwner = $this->case->participants->first(
                fn (AcceleratorCaseParticipant $participant) => $participant->user->id == $newOwnerId
            );
            if (is_null($newOwner) || is_null($newOwnerId)) {
                throw new UnprocessableEntityHttpException(__('exception.undefined_participant'));
            }
        }
    }
}
