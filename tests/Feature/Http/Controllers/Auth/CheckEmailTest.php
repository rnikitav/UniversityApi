<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Tests\Generators\User as UserGenerator;

/**
 * @group auth
 */
class CheckEmailTest extends TestAbstract
{
    public function setUp(): void
    {
        parent::setUp();
        $this->url = route('auth.check-email');
    }

    public function testCheckEmailTrue()
    {
        $user = UserGenerator::createVerified();

        $response = $this->postJson($this->url, ['email' => $user->email]);
        $response->assertOk()
            ->assertExactJson(['success' => true]);
    }

    public function testCheckEmailFalse()
    {
        UserGenerator::createVerified();

        $response = $this->postJson($this->url, ['email' => 'a@mail.ru']);
        $response->assertOk()
            ->assertExactJson(['success' => false]);
    }

    /**
     * @dataProvider incorrectEmailValues
     */
    public function testValidationIncorrectFieldOnCheckEmail($values)
    {
        $this->validationIncorrectFields($values);
    }
}
