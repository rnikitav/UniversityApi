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
        $this->acceleratorTest = AcceleratorGenerator::create($this->userAdmin);

        $this->acceleratorRepository = app()->make(AcceleratorRepository::class);
    }

    public function testGetCaseByIdOr404()
    {
        $case = AcceleratorCaseGenerator::create($this->acceleratorTest);

        $this->expectException(NotFoundHttpException::class);
        $this->acceleratorRepository->caseByIdOr404($this->acceleratorTest, 0);

        $this->assertNotNull($this->acceleratorRepository->caseByIdOr404($this->acceleratorTest, $case->id));
    }
}
