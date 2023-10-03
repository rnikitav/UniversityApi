<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Permissions\Permission;
use App\Models\User\User as UserModel;
use App\Models\Permissions\Role as RoleModel;
use App\Repositories\Permissions\Roles as RolesRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Generators\Permissions\Role as RoleGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group admin
 * @group roles
 */
class RolesTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected RoleModel $role;
    protected RolesRepository $rolesRepository;
    protected array $itemStructure = [
        'id',
        'name',
        'permissions' => [
            '*' => [
                'id',
                'name',
                'preview'
            ]
        ]
    ];
    protected array $minimalData = ['name' => 'test'];

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = UserModel::first();
        $this->role = RoleGenerator::create();

        $this->rolesRepository = app()->make(RolesRepository::class);
    }

    protected function getRoute(int $id = null): string
    {
        return route('admin.roles.index') . ($id ? '/' . $id : '');
    }

    public function testGetList()
    {
        $count = 5;
        RoleGenerator::create($count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count + 1);
    }

    public function testGetListCheckStructure()
    {
        RoleGenerator::create(5);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonStructure(['*' => $this->itemStructure]);
    }

    public function testCheckPermission()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();

        $response = $this->patchJson($this->getRoute($this->role->id));
        $response->assertForbidden();

        $user->givePermissionTo('permissions.edit');

        $response = $this->getJson($this->getRoute());
        $response->assertOk();
    }

    public function testGetItemCheckStructure()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute($this->role->id));
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);
    }

    public function testCreate()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), $this->minimalData);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure)
            ->assertJsonFragment($this->minimalData);
    }

    public function testCreateWithPermissions()
    {
        $this->actingAs($this->userAdmin);

        $permissionAdministrator = Permission::getPermissionAdministrator();
        $data = array_merge($this->minimalData, [
            'permissions' => [
                [
                    'name' => $permissionAdministrator
                ]
            ]
        ]);

        $response = $this->postJson($this->getRoute(), $data);
        $response->assertOk();

        $this->checkPermission($response->json('id'), $permissionAdministrator);
    }

    protected function checkPermission(int $roleId, string $permission): void
    {
        $createdRole = $this->rolesRepository->byId($roleId);
        $this->assertNotNull($createdRole);
        $this->assertNotNull($createdRole->permissions()->where('name', $permission)->first());
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute());
        $response->assertUnprocessable();
    }

    public function testUpdateMinimal()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($this->role->id));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $this->actingAs($this->userAdmin);

        $data = ['name' => 'test'];

        $response = $this->patchJson($this->getRoute($this->role->id), $data);
        $response->assertOk()
            ->assertJsonFragment($data);
    }

    public function testUpdateWithPermissions()
    {
        $permissionAdministrator = Permission::getPermissionAdministrator();

        $this->actingAs($this->userAdmin);

        $this->assertNull($this->role->permissions()->where('name', $permissionAdministrator)->first());

        $data = [
            'permissions' => [
                [
                    'name' => $permissionAdministrator
                ]
            ]
        ];

        $response = $this->patchJson($this->getRoute($this->role->id), $data);
        $response->assertOk();

        $this->checkPermission($this->role->id, $permissionAdministrator);
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

        $response = $this->patchJson($this->getRoute($this->role->id), $values);

        $response->assertStatus(422)->assertJsonStructure(['message']);
    }

    public function incorrectPreviewValues(): array
    {
        return [
            'name null' => [['name' => null]],
            'name string min 3' => [['name' => 'ab']],
            'name string max 255' => [['name' => Str::random(256)]],

            'permission null' => [['permissions' => ['name' => null]]],
            'permission not exists' => [['permissions' => ['name' => 'aaa']]],
        ];
    }

    public function testDelete()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->deleteJson($this->getRoute($this->role->id));
        $response->assertOk()
            ->assertExactJson(['status' => true]);

        $this->assertDatabaseEmpty('roles');
    }
}
