<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User\User as UserModel;
use App\Repositories\User\User as UserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Client as ClientModel;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use Laravel\Passport\Token;
use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class LoginLogoutTest extends TestAbstract
{
    protected PassportClientRepository $clientRepository;
    protected UserRepository $userRepository;
    protected ClientModel $client;
    protected UserModel $user;
    protected string $password = 'testtest';
    protected string $meUrl;
    protected string $logoutUrl;
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
        $this->logoutUrl = route('auth.logout');

        $this->clientRepository = app()->make(PassportClientRepository::class);
        $this->userRepository = app()->make(UserRepository::class);
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

    protected function sendRequestGetTokenLDAP(string $login): TestResponse
    {
        $data = [
            'grant_type' => 'password_active_directory',
            'client_id' => $this->client->id,
            'client_secret' => $this->client->getPlainSecretAttribute(),
            'username' => $login,
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

    public function testGetTokenLDAP()
    {
        $login = 'ldaptest1';

        $response = $this->sendRequestGetTokenLDAP($login);
        $response->assertOk()
            ->assertJsonStructure(['access_token', 'refresh_token', 'expires_in', 'token_type'])
            ->assertJsonFragment(['token_type' => 'Bearer']);

        $authData = $response->json();
        $this->authorizationHeader = $authData['token_type'] . ' ' . $authData['access_token'];

        $responseMe = $this->getJson($this->meUrl, ['Authorization' => $this->authorizationHeader]);
        $responseMe->assertOk();

        $this->assertDatabaseCount('users', 3);

        $userLDAP = $this->userRepository->byLogin($login);
        $this->assertNotNull($userLDAP);
        $this->assertTrue($userLDAP->external);
        $this->assertTrue($userLDAP->hasPermissionTo('student'));
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

    public function testGetTokenIncorrectCredentialsLDAP()
    {
        $login = 'test';

        $response = $this->sendRequestGetTokenLDAP($login);

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'invalid_grant']);
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

    public function testLogout()
    {
        $response = $this->sendRequestGetToken();
        $response->assertOk();

        $authData = $response->json();
        $this->authorizationHeader = $authData['token_type'] . ' ' . $authData['access_token'];

        $responseLogout = $this->postJson($this->logoutUrl, [], ['Authorization' => $this->authorizationHeader]);
        $responseLogout->assertOk()
            ->assertJson(['success' => true]);
    }
}
