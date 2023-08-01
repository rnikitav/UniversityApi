<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\Permissions\Permission;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\Generators\User as UserGenerator;
use Tests\TestCase;

/**
 * @group docs
 */
class DocsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected string $url;
    protected string $urlLogin;
    protected string $urlLogout;
    protected string $urlDocs;
    protected User $userAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('docs.index');
        $this->urlLogin = route('docs.login');
        $this->urlLogout = route('docs.logout');
        $this->urlDocs = route('request-docs.index');
        $this->seed();
        $this->userAdmin = User::first();
    }

    public function testUnauthenticatedIndexPage()
    {
        $response = $this->get($this->url);
        $response->assertRedirect($this->urlLogin);
    }

    public function testAuthenticatedIndexPage()
    {
        Auth::guard('web')->login($this->userAdmin);
        $response = $this->get($this->url);
        $response->assertRedirect($this->urlDocs);
    }

    public function testLogoutPage()
    {
        Auth::guard('web')->login($this->userAdmin);
        $response = $this->get($this->urlLogout);
        $response->assertRedirect($this->urlLogin);
    }

    public function testLoginPage()
    {
        $response = $this->get($this->urlLogin);
        $response->assertOk();
    }

    public function testAuthenticatedLoginPage()
    {
        Auth::guard('web')->login($this->userAdmin);
        $response = $this->get($this->urlLogin);
        $response->assertRedirect($this->urlDocs);
    }

    public function testAuthentication()
    {
        $password = 'test';
        $user = $this->createUserWithViewDocs($password);

        $response = $this->post($this->urlLogin, ['login' => $user->email, 'password' => $password]);
        $response->assertRedirect($this->url);
    }

    public function testAuthenticationIncorrect()
    {
        $password = 'test';
        $user = $this->createUserWithViewDocs($password);

        $response = $this->post($this->urlLogin, ['login' => $user->email, 'password' => '']);
        $response->assertRedirect($this->urlLogin);
    }

    protected function createUserWithViewDocs(string $password): User
    {
        $user = UserGenerator::createVerified();
        $user->setPassword($password);
        $user->givePermissionTo(Permission::findByName('docs.view', 'web'));

        return $user;
    }

    public function testAuthenticationIncorrectPermission()
    {
        $password = 'test';
        $user = UserGenerator::createVerified();
        $user->setPassword($password);

        $response = $this->post($this->urlLogin, ['login' => $user->email, 'password' => $password]);
        $response->assertRedirect($this->urlLogin);
    }
}
