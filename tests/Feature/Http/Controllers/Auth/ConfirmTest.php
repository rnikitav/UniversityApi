<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User\User as UserModel;
use Illuminate\Support\Str;
use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class ConfirmTest extends TestAbstract
{
    public function setUp(): void
    {
        parent::setUp();
        $this->url = route('auth.confirm');
    }

    public function testConfirmEmail()
    {
        $user = UserGenerator::createUnVerified();

        $response = $this->postJson($this->url, ['key' => $user->confirm_token]);
        $response->assertOk()
            ->assertExactJson(['success' => true]);

        $userUpdated = UserModel::first();
        $this->assertEquals(null, $userUpdated->confirm_token);
        $this->assertNotEquals(null, $userUpdated->email_verified_at);
    }

    public function testConfirmEmailIncorrectKey()
    {
        UserGenerator::createUnVerified();

        $response = $this->postJson($this->url, ['key' => 'test']);
        $response->assertStatus(422)
            ->assertJsonFragment(['message' => __('auth.user_not_found')]);
    }

    /**
     * @dataProvider incorrectConfirmValues
     */
    public function testValidationIncorrectFieldOnConfirm($values)
    {
        $this->validationIncorrectFields($values);
    }

    public function incorrectConfirmValues(): array
    {
        return [
            'key null' => [['key' => null]],
            'key empty string' => [['key' => '']],
            'key number' => [['key' => 123]],
            'key boolean' => [['key' => false]],
            'key long string' => [['key' => Str::random(256)]],
        ];
    }
}
