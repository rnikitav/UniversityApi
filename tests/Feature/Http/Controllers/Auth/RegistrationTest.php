<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Mail\Registration as RegistrationMail;
use App\Models\User\User as UserModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class RegistrationTest extends TestAbstract
{
    protected string $table = 'users';
    protected array $minimalRegistrationData = [
        // TODO поле удалено из таблицы users
        // "name" => "test",
        "email" => "test@test.ru",
        "password" => "testtest",
        "password_confirmation" => "testtest"
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->url = route('auth.registration');
    }

    public function testRegistration()
    {
        Mail::fake();

        $response = $this->postJson($this->url, $this->minimalRegistrationData);
        $response->assertOk()
            ->assertExactJson(['success' => true]);

        $this->assertDatabaseHas($this->table, Arr::only($this->minimalRegistrationData, ['email']));

        $user = UserModel::first();
        $this->assertFalse($user->hasVerifiedEmail());

        Mail::assertSent(function(RegistrationMail $mail) use ($user) {
            return $mail->token === $user->confirm_token;
        });
    }

    public function testRegistrationPasswordIncorrect()
    {
        $data = array_merge(
            $this->minimalRegistrationData,
            ['password_confirmation' => $this->minimalRegistrationData['password'] . '123']
        );

        $response = $this->postJson($this->url, $data);
        $response->assertStatus(422)->assertJsonStructure(['message']);
    }

    public function testRegistrationDuplicate()
    {
        $user = UserGenerator::createVerified();

        $data = array_merge($this->minimalRegistrationData, ['email' => $user->email]);

        $response = $this->postJson($this->url, $data);
        $response->assertStatus(422)->assertJsonStructure(['message']);
    }

    /**
     * @dataProvider incorrectRegistrationValues
     */
    public function testValidationIncorrectFieldOnRegistration($values)
    {
        $this->validationIncorrectFields($values);
    }

    public function incorrectRegistrationValues(): array
    {
        return array_merge($this->incorrectEmailValues(), $this->incorrectPasswordValues(),
            // TODO поле удалено из таблицы users
            // [
            //     'name null' => [['name' => null]],
            //     'name empty string' => [['name' => '']],
            //     'name number' => [['name' => 123]],
            //     'name boolean' => [['name' => false]],
            //     'name long string' => [['name' => Str::random(256)]],
            // ]
        );
    }
}
