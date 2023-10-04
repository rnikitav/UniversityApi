<?php

namespace Tests\Feature\Repositories;

use App\Models\Accelerator\Accelerator as AcceleratorModel;
use App\Models\User\User as UserModel;
use App\Repositories\Accelerator\Accelerator as AcceleratorRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Generators\Accelerator\Accelerator as AcceleratorGenerator;
use Tests\Generators\Accelerator\AcceleratorCase as AcceleratorCaseGenerator;
use Tests\TestCase;
use function app;

/**
 * @group repositories
 */
class AcceleratorTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected AcceleratorModel $acceleratorTest;
    protected AcceleratorRepository $acceleratorRepository;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = UserModel::first();
        $this->acceleratorTest = AcceleratorGenerator::createWithControlPoint($this->userAdmin);

        $this->acceleratorRepository = app()->make(AcceleratorRepository::class);
    }

    public function testGetCaseByIdOr404()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);

        $this->expectException(NotFoundHttpException::class);
        $this->acceleratorRepository->caseByIdOr404($this->acceleratorTest, 0);

        $this->assertNotNull($this->acceleratorRepository->caseByIdOr404($this->acceleratorTest, $case->id));
    }

    public function testGetEventByIdOr404()
    {
        $case = AcceleratorCaseGenerator::createWithEvent($this->acceleratorTest);
        $event = $case->events->first();

        $this->expectException(NotFoundHttpException::class);
        $this->acceleratorRepository->eventByIdOr404($case, 0);

        $this->assertNotNull($this->acceleratorRepository->eventByIdOr404($case, $event->id));
    }

    public function testGetSolutionByIdOr404()
    {
        $point = $this->acceleratorTest->controlPoints->first();
        $case = AcceleratorCaseGenerator::createWithSolution($this->acceleratorTest, $point);
        $solution = $case->solutions->first();

        $this->expectException(NotFoundHttpException::class);
        $this->acceleratorRepository->solutionByIdOr404($case, 0);

        $this->assertNotNull($this->acceleratorRepository->solutionByIdOr404($case, $solution->id));
    }
}
