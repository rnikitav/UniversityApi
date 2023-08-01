<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Mail\RetryActivate as RetryActivateMail;
use Illuminate\Support\Facades\Mail;
use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class RetryActivateTest extends TestAbstract
{
    public function setUp(): void
    {
        parent::setUp();
        $this->url = route('auth.retry-activate');
    }

    public function testRetryActivate()
    {
        Mail::fake();
        $user = UserGenerator::createUnVerified();

        $response = $this->postJson($this->url, ['email' => $user->email]);
        $response->assertOk()
            ->assertExactJson(['success' => true]);

        Mail::assertSent(function(RetryActivateMail $mail) use ($user) {
            return $mail->token === $user->confirm_token;
        });
    }

    public function testRetryActivateIncorrectEmail()
    {
        UserGenerator::createUnVerified();

        $response = $this->postJson($this->url, ['email' => 'a@mail.ru']);
        $response->assertStatus(422)
            ->assertJsonFragment(['message' =>  __('auth.user_not_found')]);
    }

    public function testRetryActivateWasVerified()
    {
        $user = UserGenerator::createVerified();

        $response = $this->postJson($this->url, ['email' => $user->email]);
        $response->assertStatus(422)
            ->assertJsonFragment(['message' => __('auth.user_is_verified', ['user' => $user->email])]);
    }

    /**
     * @dataProvider incorrectEmailValues
     */
    public function testValidationIncorrectFieldOnRetryActivate($values)
    {
        $this->validationIncorrectFields($values);
    }
}
