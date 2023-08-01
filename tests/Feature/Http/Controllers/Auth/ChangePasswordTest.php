<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User\User as UserModel;
use Illuminate\Support\Str;
use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class ChangePasswordTest extends TestAbstract
{
    protected string $newPassword = 'testtest';
    protected array $minimalData;

    public function setUp(): void
    {
        parent::setUp();
        $this->url = route('auth.change-password');
        $this->minimalData = [
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword,
        ];
    }

    public function testChangePassword()
    {
        $user = $this->makeUser();

        $response = $this->postJson($this->url, array_merge($this->minimalData, ['key' => $user->confirm_token]));
        $response->assertOk()
            ->assertExactJson(['success' => true]);

        $userUpdated = UserModel::first();

        $this->assertEquals(null, $userUpdated->confirm_token);
    }

    protected function makeUser(): UserModel
    {
        $user = UserGenerator::createVerified();
        $user->confirm_token = Str::random(40);
        $user->save();

        return $user;
    }

    public function testChangePasswordIncorrectKey()
    {
        $this->makeUser();

        $response = $this->postJson($this->url, array_merge($this->minimalData, ['key' => 'test']));
        $response->assertStatus(422)
            ->assertJsonFragment(['message' =>  __('auth.user_not_found')]);
    }

    public function testChangePasswordPasswordIncorrect()
    {
        $user = $this->makeUser();
        $data = array_merge(
            $this->minimalData,
            ['password_confirmation' => $this->newPassword . '123'],
            ['key' => $user->confirm_token]
        );

        $response = $this->postJson($this->url, $data);
        $response->assertStatus(422)->assertJsonStructure(['message']);
    }

    /**
     * @dataProvider incorrectChangePasswordValues
     */
    public function testValidationIncorrectFieldOnChangePassword($values)
    {
        $this->validationIncorrectFields($values);
    }

    public function incorrectChangePasswordValues(): array
    {
        return array_merge($this->incorrectPasswordValues(), [
            'key null' => [['key' => null]],
            'key empty string' => [['key' => '']],
            'key number' => [['key' => 123]],
            'key boolean' => [['key' => false]],
            'key long string' => [['key' => Str::random(256)]],
        ]);
    }
}
