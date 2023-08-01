<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group user_data
 * @group user_some
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'users';
    protected array $responseStructure = [
        'id',
        'email',
        'roles' => [
            '*' => [
                'id',
                'name',
                'permissions'
            ]
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = User::first();
    }

    protected function getRoute(): string
    {
        return route('user.me');
    }

    public function testGetDataCheckStructure()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonStructure($this->responseStructure);
    }
}
