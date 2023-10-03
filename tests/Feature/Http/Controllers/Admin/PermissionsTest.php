<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User\User as UserModel;
use App\Models\Permissions\Permission as PermissionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group admin
 * @group permissions
 */
class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected PermissionModel $permission;
    protected array $itemStructure = [
        'id',
        'name',
        'preview'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = UserModel::first();
        $this->permission = PermissionModel::first();
    }

    protected function getRoute(int $id = null): string
    {
        return route('admin.permissions.index') . ($id ? '/' . $id : '');
    }

    public function testGetList()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount(count(PermissionModel::getList()));
    }

    public function testGetListCheckStructure()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonStructure(['*' => $this->itemStructure]);
    }

    public function testGetListCheckPermission()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();

        $user->givePermissionTo('permissions.edit');

        $response = $this->getJson($this->getRoute());
        $response->assertOk();
    }

    public function testGetItemCheckStructure()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute($this->permission->id));
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);
    }

    public function testUpdate()
    {
        $this->actingAs($this->userAdmin);

        $data = ['preview' => 'test'];

        $response = $this->patchJson($this->getRoute($this->permission->id), $data);
        $response->assertOk()
            ->assertJsonFragment($data);
    }

    public function testUpdateFieldName()
    {
        $this->actingAs($this->userAdmin);

        $data = [
            'name' => 'test',
            'preview' => $this->permission->preview
        ];

        $response = $this->patchJson($this->getRoute($this->permission->id), $data);
        $response->assertOk()
            ->assertJsonFragment(['name' => $this->permission->name]);
    }

    /**
     * @dataProvider incorrectPreviewValues
     */
    public function testValidationIncorrectPreview($values)
    {
        $this->validationIncorrectFields($values);
    }

    protected function validationIncorrectFields(array $values): void
    {
        $this->actingAs($this->userAdmin);
        $response = $this->patchJson($this->getRoute($this->permission->id), $values);
        $response->assertStatus(422)->assertJsonStructure(['message']);
    }

    public function incorrectPreviewValues(): array
    {
        return [
            'preview null' => [['preview' => null]],
            'preview string min 3' => [['preview' => 'ab']],
            'preview string max 255' => [['preview' => Str::random(256)]],
        ];
    }
}
