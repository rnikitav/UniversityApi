<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Mail\Forgot as ForgotMail;
use App\Models\User\User as UserModel;
use Illuminate\Support\Facades\Mail;
use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class ForgotTest extends TestAbstract
{
    public function setUp(): void
    {
        parent::setUp();
        $this->url = route('auth.forgot');
    }

    public function testForgot()
    {
        Mail::fake();
        $user = UserGenerator::createVerified();

        $response = $this->postJson($this->url, ['email' => $user->email]);
        $response->assertOk()
            ->assertExactJson(['success' => true]);

        $userUpdated = UserModel::first();

        $this->assertNotEquals(null, $userUpdated->confirm_token);

        Mail::assertSent(function(ForgotMail $mail) use ($userUpdated) {
            return $mail->token === $userUpdated->confirm_token;
        });
    }

    public function testForgotIncorrectEmail()
    {
        UserGenerator::createVerified();

        $response = $this->postJson($this->url, ['email' => 'a@mail.ru']);
        $response->assertStatus(422)
            ->assertJsonFragment(['message' =>  __('auth.user_not_found')]);
    }

    public function testForgotNotVerified()
    {
        $user = UserGenerator::createUnVerified();

        $response = $this->postJson($this->url, ['email' => $user->email]);
        $response->assertStatus(403)
            ->assertJsonFragment([
                'message' =>  __('auth.email_not_verified', ['email' => $user->email]),
                'status_code' => 'not_verified'
            ]);
    }

    /**
     * @dataProvider incorrectEmailValues
     */
    public function testValidationIncorrectFieldOnForgot($values)
    {
        $this->validationIncorrectFields($values);
    }
}
