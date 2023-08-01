<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\User\User;
use App\Models\User\User as UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

abstract class AbstractUserDataTestCase extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected UserModel $userSimple;
    protected string $table = '';
    protected array $responseStructure = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = User::first();
        $this->userSimple = UserGenerator::createVerified();
    }

    abstract protected function getRoute($user_id): string;

    public function testCheckUserIdParameter()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute($this->userAdmin->id));
        $response->assertOk();

        $response = $this->getJson($this->getRoute(0));
        $response->assertNotFound();

        $response = $this->getJson($this->getRoute('abc'));
        $response->assertNotFound();
    }

    public function testCheckPermission()
    {
        $this->actingAs($this->userSimple);

        $response = $this->getJson($this->getRoute($this->userAdmin->id));
        $response->assertStatus(403)->assertJsonFragment(['message' => __('exception.not_permitted')]);

        $response = $this->getJson($this->getRoute($this->userSimple->id));
        $response->assertOk();

        $this->userSimple->givePermissionTo('user.edit');

        $response = $this->getJson($this->getRoute($this->userAdmin->id));
        $response->assertOk();
    }
}
