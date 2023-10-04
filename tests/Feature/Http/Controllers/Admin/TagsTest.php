<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User\User as UserModel;
use App\Models\Tags\Tag as TagModel;
use App\Repositories\Tags\Tag as TagRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\Generators\Tag as TagGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\Generators\ImageCollection as ImageCollectionGenerator;
use Tests\TestCase;

/**
 * @group admin
 * @group tags
 */
class TagsTest extends TestCase
{
    use RefreshDatabase;

    protected UserModel $userAdmin;
    protected TagRepository $tagRepository;
    protected array $itemStructure = [
        'id',
        'name',
        'image_collections' => [
            '*' => [
                'id',
                'name',
                'attachments'
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
        $this->tagRepository = app()->make(TagRepository::class);
    }

    protected function getRoute(int $id = null): string
    {
        return route('admin.tags.index') . ($id ? '/' . $id : '');
    }

    public function testGetList()
    {
        $count = 5;
        TagGenerator::create($count);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonCount($count, 'data')
            ->assertJsonStructure(['data' => ['*' => $this->itemListStructure]]);
    }

    public function testGetListPaginate()
    {
        $count = 5;
        TagGenerator::create($count);

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

        $user->givePermissionTo('tags.edit');

        $response = $this->getJson($this->getRoute());
        $response->assertOk();
    }

    public function testGetItemCheckStructure()
    {
        $imageCollection = TagGenerator::create();
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

        /** @var TagModel $model */
        $model = $this->tagRepository->byId($response->json('id'));
        $this->assertNotNull($model);
    }

    public function testCreateWithImageCollections()
    {
        $count = 2;
        $imageCollections = ImageCollectionGenerator::create($count);
        $this->actingAs($this->userAdmin);

        $data = $this->minimalCreateData;
        $data['image_collections'] = [
            ['id' => $imageCollections[0]->id],
            ['id' => $imageCollections[1]->id],
        ];

        $response = $this->postJson($this->getRoute(), $data);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        /** @var TagModel $model */
        $model = $this->tagRepository->byId($response->json('id'));
        $this->assertNotNull($model);
        $this->assertCount($count, $model->imageCollections);
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'name'));
        $response->assertUnprocessable();
    }

    public function testUpdateMinimal()
    {
        $tag = TagGenerator::create();
        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($tag->id));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $count = 2;
        $imageCollections = ImageCollectionGenerator::create($count);
        $tag = TagGenerator::create();
        $this->actingAs($this->userAdmin);

        $data = $this->minimalCreateData;
        $data['image_collections'] = [
            ['id' => $imageCollections[0]->id],
            ['id' => $imageCollections[1]->id],
        ];

        $response = $this->patchJson($this->getRoute($tag->id), $data);
        $response->assertOk()
            ->assertJsonFragment(['id' => $tag->id]);

        $tag->refresh();
        $this->assertEquals($this->minimalCreateData['name'], $tag->name);
        $this->assertCount($count, $tag->imageCollections);
    }

    public function testDelete()
    {
        $tag = TagGenerator::create();
        $this->actingAs($this->userAdmin);

        $response = $this->deleteJson($this->getRoute($tag->id));
        $response->assertOk()
            ->assertExactJson(['status' => true]);

        $this->assertNull($this->tagRepository->byId($tag->id));
    }
}
