<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User\User as UserModel;
use App\Models\Tags\ImageCollection as ImageCollectionModel;
use App\Repositories\Tags\ImageCollection as ImageCollectionRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\Generators\ImageCollection as ImageCollectionGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group admin
 * @group image_collections
 */
class ImageCollectionsTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected ImageCollectionRepository $imageCollectionRepository;
    protected array $itemStructure = [
        'id',
        'name',
        'attachments' => [
            '*' => [
                'category',
                'path',
                'original_name',
            ]
        ],
    ];
    protected array $itemListStructure = [
        'id',
        'name',
    ];
    protected array $minimalCreateData = [
         'name' => 'test name',
    ];

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->userAdmin = UserModel::first();
        $this->imageCollectionRepository = app()->make(ImageCollectionRepository::class);
    }

    protected function getRoute(int $id = null): string
    {
        return route('admin.image-collections.index') . ($id ? '/' . $id : '');
    }

    public function testGetList()
    {
        $count = 5;
        ImageCollectionGenerator::create($count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count, 'data')
            ->assertJsonStructure(['data' => ['*' => $this->itemListStructure]]);
    }

    public function testGetListPaginate()
    {
        $count = 5;
        ImageCollectionGenerator::create($count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute() . '?' . http_build_query(['lazy' => true]));
        $response->assertOk()
            ->assertJsonStructure(['data', 'cursor'])
            ->assertJsonCount($count, 'data');
    }

    public function testCheckPermission()
    {
        $user = UserGenerator::createVerified();
        $this->actingAs($user);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();

        $user->givePermissionTo('image-collection.edit');

        $response = $this->getJson($this->getRoute());
        $response->assertOk();
    }

    public function testGetItemCheckStructure()
    {
        $imageCollection = ImageCollectionGenerator::create();
        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute($imageCollection->id));
        $response->assertOk()
            ->assertJsonFragment(['id' => $imageCollection->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testCreate()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        /** @var ImageCollectionModel $model */
        $model = $this->imageCollectionRepository->byId($response->json('id'));
        $this->assertNotNull($model);
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'name'));
        $response->assertUnprocessable();
    }

    public function testUpdateMinimal()
    {
        $imageCollection = ImageCollectionGenerator::create();
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($imageCollection->id));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $imageCollection = ImageCollectionGenerator::create();
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($imageCollection->id), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonFragment(['id' => $imageCollection->id]);

        $imageCollection->refresh();
        $this->assertEquals($this->minimalCreateData['name'], $imageCollection->name);
    }

    public function testDelete()
    {
        $imageCollection = ImageCollectionGenerator::create();
        $this->actingAs($this->userAdmin);

        $response = $this->deleteJson($this->getRoute($imageCollection->id));
        $response->assertOk()
            ->assertExactJson(['status' => true]);

        $this->assertNull($this->imageCollectionRepository->byId($imageCollection->id));
    }
}
