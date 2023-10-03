<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User\User as UserModel;
use App\Models\News\News as NewsModel;
use App\Repositories\News\NewsRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Storage;
use Tests\Generators\News as NewsGenerator;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    protected NewsRepository $newsRepository;
    protected UserModel $userAdmin;
    protected UserModel $userNoAdmin;

    protected array $itemStructure = [
        'id',
        'title',
        'body',
        'slug',
        'files' =>[
        '*' => [
            'category',
            'path',
            'original_name',
        ]
    ],
        'published_at',
        'created_at',
        'updated_at',
    ];
    protected array $minimalCreateData = [
        'title' => 'test title',
        'body' => '<h1>test body</h1>',
    ];

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->userAdmin = UserModel::first();
        $this->userNoAdmin = UserGenerator::createVerified();
        Storage::fake('testing');
        $file = UploadedFile::fake()->create('file.webp');
        $this->minimalCreateData['img'] = $file;
        $this->minimalCreateData['img_preview'] = $file;


        $this->newsRepository = app()->make(NewsRepository::class);
    }

    protected function getRoute(int $id = null): string
    {
        return route('admin.news.index') . ($id ? '/' . $id : '');
    }

    public function testGetOneNewsCheckStructure()
    {
        NewsGenerator::create(1);
        $news = NewsModel::first();

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute($news->id));

        $response->assertOk()
            ->assertJsonFragment(['id' => $news->id])
            ->assertJsonStructure($this->itemStructure);
    }

    public function testGetList()
    {
        $count = 5;
        NewsGenerator::create($count);


        $this->actingAs($this->userNoAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());


        $response->assertOk()
            ->assertJsonCount($count, 'data');
    }

    public function testGetListPaginate()
    {
        $count = 5;
        NewsGenerator::create($count);


        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute() . '?' . http_build_query(['lazy' => true]));
        $response->assertOk()
            ->assertJsonStructure(['data', 'cursor'])
            ->assertJsonCount($count, 'data');
    }

    public function testGetListCheckStructure()
    {
        NewsGenerator::create(5);

        $this->actingAs($this->userAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertOk()
            ->assertJsonStructure(['data' => ['*' => $this->itemStructure]]);
    }
    public function testCheckPermission()
    {
        $this->actingAs($this->userNoAdmin);

        $response = $this->getJson($this->getRoute());
        $response->assertForbidden();

        $this->userNoAdmin->givePermissionTo('news.edit');

        $response = $this->getJson($this->getRoute());
        $response->assertOk();
    }

    public function testCreate()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonStructure($this->itemStructure);

        /** @var NewsModel $newsModel */
        $newsModel = $this->newsRepository->byId($response->json('id'));
        $this->assertNotNull($newsModel);
        $this->assertEquals($this->minimalCreateData['title'], $newsModel->title);
    }

    public function testCreateIncorrect()
    {
        $this->actingAs($this->userAdmin);

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'title'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'body'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'img_preview'));
        $response->assertUnprocessable();

        $response = $this->postJson($this->getRoute(), Arr::except($this->minimalCreateData, 'img'));
        $response->assertUnprocessable();
    }

    public function testUpdateMinimal()
    {
        NewsGenerator::create(1);
        $news = NewsModel::first();

        $this->actingAs($this->userAdmin);

        $response = $this->patchJson($this->getRoute($news->id));
        $response->assertOk();
    }

    public function testUpdate()
    {
        $this->actingAs($this->userAdmin);

        NewsGenerator::create(1);
        $news = NewsModel::first();


        $response = $this->patchJson($this->getRoute($news->id), $this->minimalCreateData);
        $response->assertOk()
            ->assertJsonFragment(['id' => $news->id]);

        $news->refresh();
        $this->assertEquals($this->minimalCreateData['body'], $news->body);
    }

    public function testDelete()
    {
        $this->actingAs($this->userAdmin);

        NewsGenerator::create(1);
        $news = NewsModel::first();


        $response = $this->deleteJson($this->getRoute($news->id));
        $response->assertOk()
            ->assertExactJson(['status' => true]);

        $this->assertNull($this->newsRepository->byId($news->id));
    }


}
