<?php

namespace App\Http\Controllers\Accelerator;

use App\Exceptions\OperationNotPermittedException;
use App\Http\Requests\Accelerator\Case\CreateSolution as CreateRequest;
use App\Http\Requests\Accelerator\Case\UpdateSolution as UpdateRequest;
use App\Http\Requests\Accelerator\Case\SolutionMessage as SolutionMessageRequest;
use App\Http\Resources\Accelerator\Solution as SolutionResource;
use App\Models\Accelerator\Case\AcceleratorCaseSolution;
use App\Models\Permissions\Permission;
use App\Utils\DB as DBUtils;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class AcceleratorCaseSolutionController extends AbstractAcceleratorCaseController
{
    protected ?AcceleratorCaseSolution $solution;
//    protected ?AcceleratorCaseParticipant $processedParticipant;

    public function index(int $id, int $case_id): Response
    {
        $this->getAcceleratorCase($id, $case_id);
        return response(SolutionResource::collection($this->case->solutions));
    }

    /**
     * @throws Throwable
     */
    public function store(CreateRequest $request, int $id, int $case_id): Response
    {
        $data = $request->prepareData();

        $this->getAcceleratorCase($id, $case_id);

        $this->checkPermissionStore($data['control_point_id']);

        $new = DBUtils::inTransaction(function () use ($data) {
            /** @var AcceleratorCaseSolution $new */
            $new = $this->case->solutions()
                ->make($data);
            $new->setAttachments($data['files'] ?? []);
            $new->save();

            return $new;
        });

        return response(new SolutionResource($new->refresh()));
    }

    public function show(int $id, int $case_id, int $solution_id): Response
    {
        $this->getAcceleratorCase($id, $case_id);

        $solution = $this->acceleratorRepository->solutionByIdOr404($this->case, $solution_id);

        return response(new SolutionResource($solution));
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, int $id, int $case_id, int $solution_id): Response
    {
        $data = $request->prepareData();

        $this->getAcceleratorCase($id, $case_id);

        $this->solution = $this->acceleratorRepository->solutionByIdOr404($this->case, $solution_id);
        $this->checkPermissionUpdate();

        if (array_key_exists('score', $data) && $data['score'] > $this->solution->controlPoint->max_score) {
            throw new UnprocessableEntityHttpException(__('exception.score_more'));
        }

        DBUtils::inTransaction(function () use ($data) {
            $this->solution->update($data);

            if (count($data['messages'])) {
                $this->solution->setMessages($data['messages']);
                $this->solution->update();
            }
        });

        return response(new SolutionResource($this->solution->refresh()));
    }

    public function sendMessage(SolutionMessageRequest $request, int $id, int $case_id, int $solution_id): Response
    {
        $data = $request->prepareData();

        $this->checkIsExpert();

        $this->getAcceleratorCase($id, $case_id);

        $this->solution = $this->acceleratorRepository->solutionByIdOr404($this->case, $solution_id);

        $this->solution->setMessages($data['messages']);
        $this->solution->update();

        return response(new SolutionResource($this->solution->refresh()));
    }

    protected function checkPermissionStore(int $pointId): void
    {
        if ($this->case->participants->doesntContain('user_id', $this->currentUser->id)) {
            throw new OperationNotPermittedException();
        }

        $prevPoint = $findPoint = null;
        foreach ($this->accelerator->controlPoints->sortBy('date_completion') as $point) {
            if ($point->id == $pointId) {
                $findPoint = $point;
                break;
            }
            $prevPoint = $point;
        }

        if (now()->isAfter($findPoint->date_completion->endOfDay())) {
            throw new OperationNotPermittedException();
        }

        if (!is_null($prevPoint)) {
            /** @var AcceleratorCaseSolution $prevSolution */
            $prevSolution = $this->case->solutions->firstWhere('control_point_id', $prevPoint->id);
            if (is_null($prevSolution) || !$prevSolution->isApproved()) {
                throw new OperationNotPermittedException();
            }
        }
    }

    protected function checkPermissionUpdate(): void
    {
        $isOwner = $this->currentUser->is($this->accelerator->user);
        if (!$isOwner) {
            throw new OperationNotPermittedException();
        }
    }
}
