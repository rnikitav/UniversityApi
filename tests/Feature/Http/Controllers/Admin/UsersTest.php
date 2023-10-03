<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Permissions\Permission;
use App\Models\User\User as UserModel;
use App\Repositories\User\User as UserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\Generators\Permissions\Role as RoleGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group admin
 * @group users
 */
class UsersTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected UserRepository $userRepository;
    protected array $itemStructure = [
        'id',
        'main_data' => [
            'first_name',
            'last_name',
            'patronymic',
            'email'
        ],
        'roles' => [
            '*' => [
                'id',
                'name',
                'permissions'
            ]
        ],
    ];
    protected array $itemListStructure = [
        'id',
        'main_data' => [
            'first_name',
            'last_name',
            'patronymic',
            'email'
        ]
    ];
    protected array $minimalCreateData = [
         'email' => 'test@test.ru',
         'password' => 'password',
         'password_confirmation' => 'password',
     ];
    protected array $mainData = [
        'first_name' => 'test',
        'last_name' => 'test',
        'patronymic' => 'test',
    ];

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = UserModel::first();

        $this->userRepository = app()->make(UserRepository::class);
    }

    protected function getRoute(int $id = null): string
    {
        return route('admin.users.index') . ($id ? '/' . $id : '');
    }

    public function testGetList()
    {
        $count = 5;
        UserGenerator::createVerified($count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count + 1, 'data');
    }

    public function testGetListPaginate()
    {
        $count = 5;
        UserGenerator::createVerified($count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute() . '?' . http_build_query(['lazy' => true]));
        $response->assertOk()
            ->assertJsonStructure(['data', 'cursor'])
            ->assertJsonCount($count + 1, 'data');
    }

    public function testGetListCheckStructure()
    {
        UserGenerator::createVerified(5);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonStructure(['data' => ['*' => $this->itemListStructure]]);
    }

    public function testCheckPermission()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();

        $user->givePermissionTo('users.edit');

        $response = $this->getJson($this->getRoute());
        $response->assertOk();
    }

    public function testGetItemCheckStructure()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute($this->userAdmin->id));
        $response->assertOk()
            ->assertJsonFragment(['id' => $this->userAdmin->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testCreate()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        /** @var UserModel $userModel */
        $userModel = $this->userRepository->byId($response->json('id'));
        $this->assertNotNull($userModel);
        $this->assertFalse($userModel->external);
        $this->assertEquals($this->minimalCreateData['email'], $userModel->login);
    }

    public function testCreateWithPermissions()
    {
        $this->actingAs($this->userAdmin);

        $permissionAdministrator = Permission::getPermissionAdministrator();

        $role = RoleGenerator::create();
        $role->givePermissionTo($permissionAdministrator);

        $data = array_merge($this->minimalCreateData, [
            'roles' => [['id' => $role->id]]
        ]);

        $response = $this->postJson($this->getRoute(), $data);
        $response->assertOk();

        /** @var UserModel $userModel */
        $userModel = $this->userRepository->byId($response->json('id'));
        $this->assertNotNull($userModel);
        $this->assertTrue($userModel->checkPermissionTo($permissionAdministrator));
    }

    public function testCreateWithMainData()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), array_merge($this->minimalCreateData, $this->mainData));
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure)
            ->assertJsonFragment([
                'main_data' => array_merge($this->mainData, ['email' => $this->minimalCreateData['email']])
            ]);
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'email'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'password'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'password_confirmation'));
        $response->assertUnprocessable();
    }

    public function testUpdateMinimal()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($this->userAdmin->id));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $this->actingAs($this->userAdmin);

        $user = UserGenerator::createVerified();

        $response = $this->patchJson($this->getRoute($user->id), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonFragment(['id' => $user->id]);

        $user->refresh();
        $this->assertEquals($this->minimalCreateData['email'], $user->login);
    }

    public function testUpdateWithPermissions()
    {
        $this->actingAs($this->userAdmin);

        $user = UserGenerator::createVerified();

        $permissionAdministrator = Permission::getPermissionAdministrator();

        $role = RoleGenerator::create();
        $role->givePermissionTo($permissionAdministrator);

        $data = ['roles' => [['id' => $role->id]]];

        $response = $this->patchJson($this->getRoute($user->id), $data);
        $response->assertOk();

        $user->refresh();
        $this->assertTrue($user->checkPermissionTo($permissionAdministrator));
    }

    public function testUpdateWithMainData()
    {
        $this->actingAs($this->userAdmin);

        $user = UserGenerator::createVerified();
        $user->mainData()->update(['email' => $user->login]);

        $response = $this->patchJson($this->getRoute($user->id), $this->mainData);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure)
            ->assertJsonFragment([
                'main_data' => array_merge($this->mainData, ['email' => $user->login])
            ]);
    }

    public function testUpdateExternal()
    {
        $this->actingAs($this->userAdmin);

        $user = UserGenerator::createExternal();
        $firstLogin = $user->login;

        $response = $this->patchJson($this->getRoute($user->id), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonFragment(['id' => $user->id]);

        $user->refresh();
        $this->assertEquals($firstLogin, $user->login);
        $this->assertEquals($this->minimalCreateData['email'], $user->mainData->email);
    }

    public function testUpdateDuplicateEmail()
    {
        $this->actingAs($this->userAdmin);

        $user = UserGenerator::createVerified();

        $response = $this->patchJson($this->getRoute($user->id), ['email' => $this->userAdmin->mainData->email]);
        $response->assertUnprocessable();
    }

    /**
     * @dataProvider incorrectPreviewValues
     */
    public function testValidationIncorrectFields($values)
    {
        $this->validationIncorrectFields($values);
    }

    protected function validationIncorrectFields(array $values): void
    {
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($this->userAdmin->id), $values);

        $response->assertUnprocessable()->assertJsonStructure(['message']);
    }

    public function incorrectPreviewValues(): array
    {
        return [
            'email string' => [['email' => 'test']],

            'password min 8' => [['password' => '1234567']],

            'role not exists' => [['roles' => [['id' => 0]]]],
        ];
    }

    public function testDelete()
    {
        $this->actingAs($this->userAdmin);

        $user = UserGenerator::createVerified();

        $response = $this->deleteJson($this->getRoute($user->id));
        $response->assertOk()
            ->assertExactJson(['status' => true]);

        $this->assertNull($this->userRepository->byId($user->id));
    }
}
