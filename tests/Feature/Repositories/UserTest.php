<?php

namespace Tests\Feature\Repositories;

use App\Repositories\User\User as UserRepository;
use App\Models\User\User as UserModel;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;
use function app;

/**
 * @group repositories
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $users;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionsSeeder::class);
        $this->users = app()->make(UserRepository::class);
    }

    public function testGetAll()
    {
        $count = 10;
        $this->assertDatabaseCount(UserModel::class, 0);

        UserGenerator::createVerified($count);

        $vacancies = $this->users->all();
        $this->assertCount($count, $vacancies);
    }

    public function testGetById()
    {
        $count = 10;
        $items = UserGenerator::createVerified($count);
        $item = $items[0];

        $vacancy = $this->users->byId($item->id);
        $this->assertNotNull($vacancy);
    }

    public function testGetByIdOr404()
    {
        $count = 10;
        $items = UserGenerator::createVerified($count);
        $item = $items[0];

        $vacancy = $this->users->byIdOr404($item->id);
        $this->assertNotNull($vacancy);

        $this->expectException(NotFoundHttpException::class);
        $this->users->byIdOr404(-1);
    }

    public function testGetByIds()
    {
        $count = 10;
        $items = UserGenerator::createVerified($count);

        $vacancies = $this->users->byIds($items->pluck('id')->toArray());
        $this->assertCount($count, $vacancies);
    }

    public function testGetPaginate()
    {
        $count = 10;
        $items = UserGenerator::createVerified($count);

        $cursor = null;
        foreach ($items as $item) {
            $vacancies = $this->users->paginate(1, 'id', $cursor);
            $this->assertCount(1, $vacancies);
            $this->assertEquals($item->id, $vacancies[0]->id);

            $cursor = $vacancies->nextCursor()?->encode();
        }
    }

    public function testGetWithPermission()
    {
        $count = 3;
        $permission = 'users.edit';
        $verifiedUsers = UserGenerator::createVerified($count);
        UserGenerator::createVerified($count);

        foreach ($verifiedUsers as $user) {
            $user->givePermissionTo($permission);
        }

        $usersWithPermission = $this->users->withPermission($permission);
        $this->assertCount($count, $usersWithPermission);
    }

    public function testGetByLogin()
    {
        $user = UserGenerator::createVerified();

        $userModel = $this->users->byLogin($user->login);
        $this->assertEquals($user->id, $userModel->id);

        $userNotFound = $this->users->byLogin('a@mail.ru');
        $this->assertNull($userNotFound);
    }

    public function testGetByConfirmToken()
    {
        $user = UserGenerator::createUnVerified();

        $userModel = $this->users->byConfirmToken($user->confirm_token);
        $this->assertEquals($user->id, $userModel->id);

        $userNotFound = $this->users->byConfirmToken('test');
        $this->assertNull($userNotFound);
    }
}
