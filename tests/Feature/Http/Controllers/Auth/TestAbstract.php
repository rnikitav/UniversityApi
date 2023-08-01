<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestAbstract extends TestCase
{
    use RefreshDatabase;

    protected string $url = '';

    protected function incorrectEmailValues(): array
    {
        return [
            'email null' => [['email' => null]],
            'email empty string' => [['email' => '']],
            'email number' => [['email' => 123]],
            'email string' => [['email' => 'test']],
        ];
    }

    protected function incorrectPasswordValues(): array
    {
        return [
            'password boolean' => [['password' => false]],
            'password string min 8' => [['password' => 'testtes']],
            'password number' => [['password' => 123]],
        ];
    }

    protected function validationIncorrectFields(array $values): void
    {
        $response = $this->postJson($this->url, $values);
        $response->assertStatus(422)->assertJsonStructure(['message']);
    }
}
