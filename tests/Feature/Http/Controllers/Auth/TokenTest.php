<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User\User as UserModel;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Client as ClientModel;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class TokenTest extends TestAbstract
{
    protected PassportClientRepository $clientRepository;
    protected ClientModel $client;
    protected UserModel $user;
    protected string $password = 'testtest';
    protected string $meUrl;
    protected string $authorizationHeader;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->url = route('auth.token');
        $this->meUrl = route('user.me');

        $this->clientRepository = app()->make(PassportClientRepository::class);
        $this->client = $this->createClient();

        $this->user = UserGenerator::createVerified();
        $this->user->setPassword($this->password);
    }

    protected function createClient(): ClientModel
    {
        return $this->clientRepository->createPasswordGrantClient(
            null, 'client', 'http://localhost', 'users'
        );
    }

    protected function sendRequestGetToken(): TestResponse
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->getPlainSecretAttribute(),
            'username' => $this->user->login,
            'password' => $this->password,
            'scope' => null
        ];

        return $this->postJson($this->url, $data);
    }

    protected function sendRequestRefreshToken(string $refreshToken): TestResponse
    {
        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->getPlainSecretAttribute(),
            'refresh_token' => $refreshToken,
            'scope' => null
        ];

        return $this->postJson($this->url, $data);
    }

    public function testGetToken()
    {
        $response = $this->sendRequestGetToken();
        $response->assertOk()
            ->assertJsonStructure(['access_token', 'refresh_token', 'expires_in', 'token_type'])
            ->assertJsonFragment(['token_type' => 'Bearer']);

        $authData = $response->json();
        $this->authorizationHeader = $authData['token_type'] . ' ' . $authData['access_token'];

        $responseMe = $this->getJson($this->meUrl, ['Authorization' => $this->authorizationHeader]);
        $responseMe->assertOk();
    }

    public function testRefreshToken()
    {
        $response = $this->sendRequestGetToken();

        if ($response->status() == 200) {
            $authData = $response->json();
            $this->authorizationHeader = $authData['token_type'] . ' ' . $authData['access_token'];

            $responseRefresh = $this->sendRequestRefreshToken($authData['refresh_token']);
            $responseRefresh->assertOk()
                ->assertJsonStructure(['access_token', 'refresh_token', 'expires_in', 'token_type'])
                ->assertJsonFragment(['token_type' => 'Bearer']);
            $authData = $responseRefresh->json();

            $this->authorizationHeader = $authData['token_type'] . ' ' . $authData['access_token'];
            $responseMe = $this->getJson($this->meUrl, ['Authorization' => $this->authorizationHeader]);
            $responseMe->assertOk();
        }
    }

    public function testGetTokenIncorrectGrantType()
    {
        $data = [
            'grant_type' => 'test',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->getPlainSecretAttribute(),
            'username' => $this->user->login,
            'password' => $this->password,
            'scope' => null
        ];

        $response = $this->postJson($this->url, $data);

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'unsupported_grant_type']);
    }

    public function testGetTokenIncorrectClient()
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => 0,
            'client_secret' => '',
            'username' => $this->user->login,
            'password' => $this->password,
            'scope' => null
        ];

        $response = $this->postJson($this->url, $data);

        $response->assertStatus(401)
            ->assertJsonFragment(['error' => 'invalid_client']);
    }

    public function testGetTokenIncorrectCredentials()
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->getPlainSecretAttribute(),
            'username' => $this->user->login,
            'password' => '',
            'scope' => null
        ];

        $response = $this->postJson($this->url, $data);

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'invalid_request']);
    }

    public function testGetTokenIncorrectRefreshToken()
    {
        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->getPlainSecretAttribute(),
            'refresh_token' => '',
            'scope' => null
        ];

        $response = $this->postJson($this->url, $data);

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'invalid_request']);
    }

}
